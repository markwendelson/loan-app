<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Enums\PaymentType;
use App\Http\Requests\LoanRequest;
use App\Models\Attachment;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\DocumentType;
use App\Models\InsuranceFeeSchedule;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanDisbursementDetails;
use App\Models\LoanFee;
use App\Models\LoanProduct;
use App\Models\LoanType;
use App\Models\LoanWithdrawRequest;
use App\Models\Savings;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct', 'loanProduct.loanType', 'borrower.details')->filter($search)
                ->whereNotNull('borrower_id')
                // Apply filter only if the user is not an admin or super_admin
                ->whereHas('borrower', function ($query) {
                    $query->when(!auth()->user()->hasRole('admin|super_admin'), function ($query) {
                        $query->where('branch_id', auth()->user()->current_branch_id);
                    });
                })
                ->orderByDesc('loan_date')
                ->orderByDesc('loan_no');

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $loans->count();
            $loans = $loans->skip($start)
            ->take($length)
            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $previous_loan = Loan::with('borrower.details', 'payments', 'loanCharges')->where('loan_no', $request->loan_no_reference)->first();
        $reloan = $request->reloan;
        $reloan_deduction = '';
        $principal_loan_balance = '';
        $special_loan_balance = '';
        if ($previous_loan && $reloan) {
            $reloan_date = date('Y-m-d');
            $amt = $this->calculateReloanDeduction($previous_loan, $reloan_date);
            $reloan_deduction = $amt['reloan_deduction'];
            $principal_loan_balance = $amt['principal_loan_balance'];
            $special_loan_balance = $amt['special_loan_balance'];
        }

        if ($previous_loan) {
            $clients = Client::where('id', $previous_loan->borrower_id)->get();
        } else {
            $clients = Client::active()->get();
        }
        $additional_loan = $request->additional_loan;
        if ($previous_loan && $additional_loan) {
            $loan_products = LoanProduct::where('id', $previous_loan->loan_product_id)->get();
        } else {
            $loan_products = LoanProduct::active()->get();
        }

        // $loan_co_makers = [];
        // if($previous_loan) {
        //     $ploan = Loan::where('id', $previous_loan->id)->first();
        //     if($ploan) {
        //         $loan_co_makers = $ploan->borrower->coMakers->filter(function ($comaker) use ($ploan) {
        //             $loan_balance = getClientTotalLoanBalance($comaker);
        //             $comaker->loan_balance = number_format($loan_balance, 2);
        //             return $comaker->pivot->loan_id == $ploan->id;
        //         });
        //     }
        // }

        return view('loan.create', compact('loan_products', 'clients', 'reloan_deduction', 'previous_loan', 'principal_loan_balance', 'special_loan_balance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LoanRequest $request)
    {
        $data = $request->all();

        if (!empty($data['is_reloan']) && filter_var($data['is_reloan'], FILTER_VALIDATE_BOOLEAN)) {
            return $this->processReloan($data);
        }

        if (!empty($data['is_additional_loan']) && filter_var($data['is_additional_loan'], FILTER_VALIDATE_BOOLEAN)) {
            return $this->processAdditionalLoan($data);
        }

        try {

            $loan_product = LoanProduct::with('prerequisite')->find($data['loan_product_id']);
            if ($loan_product->prerequisite->isNotEmpty()) {
                $data['is_special'] = 1;

                // must have principal loan
                $principal_loan = Loan::where('borrower_id', $data['borrower_id'])
                    ->where('status', LoanStatus::APPROVE)
                    ->where('is_special', 0)
                    ->first();

                if (!in_array($principal_loan->loan_product_id, $loan_product->prerequisite->pluck('id')->toArray())) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Required prerequisite loan is missing.',
                    ], 400);
                }

                // $alreadyHasLoan = Loan::where([
                //                 'borrower_id' => $data['borrower_id'],
                //                 'is_special' => 1
                //             ])
                //             ->where('status', LoanStatus::APPROVE)
                //             ->orWhere('status', LoanStatus::REQUEST_APPROVE)
                //             ->orWhere('status', LoanStatus::PENDING)
                //             ->first();

                // if($alreadyHasLoan) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Client already has a pending or approved loan']
                //         , 400);
                // }

            } else {
                $data['is_special'] = 0;

                $alreadyHasLoan = Loan::where('borrower_id', $data['borrower_id'])
                    ->whereIn('status', [
                        LoanStatus::APPROVE,
                        LoanStatus::REQUEST_APPROVE,
                        LoanStatus::PENDING
                    ])
                    ->first();

                $loan_types = LoanType::get('name');
                $limits = [];
                foreach ($loan_types as $type) {
                    $limits[Str::slug($type->name)] = 1;
                }

                // dd($loan_product->loanType->name == Str::slug($alreadyHasLoan->loanProduct->loanType->name));

                if ($alreadyHasLoan && Str::slug($loan_product->loanType->name) !== 'mortgage-loan') {
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => 'Client already has a pending or approved loan'
                        ]
                        ,
                        400
                    );
                }

            }

            DB::transaction(function () use ($data) {
                $loan = Loan::create($data);

                $borrower = Client::findOrFail($data['borrower_id']);
                if(isset($data['client_comaker'])) {
                    $borrower->coMakers()->syncWithoutDetaching(
                        array_fill_keys($data['client_comaker'], ['loan_id' => $loan->id])
                    );
                }

                LoanCharge::create([
                    'loan_id' => $loan->id,
                    'advance_interest' => $data['advance_interest'],
                    'service_fee' => $data['service_fee'],
                    'insurance_fee' => $data['insurance_fee'],
                    'notary_fee' => $data['notary_fee'],
                    'membership_fee' => config('settings.membership_fee'),
                    'membership_fee_paid' => $data['membership_fee'],
                    'redeem_charges' => $data['redeem_charges'],
                    'principal_advance' => $data['principal_advance'],
                    'interest_advance' => $data['interest_advance'],
                    'paid_interest_advance' => $data['paid_interest_advance'],
                ]);

                $insurance = InsuranceFeeSchedule::where('min_amount', '<=', $data['applied_amount'])
                    ->where('max_amount', '>=', $data['applied_amount'])->first();

                LoanFee::create([
                    'loan_id' => $loan->id,
                    'insurance_monthly_fee' => $insurance->monthly_fee,
                    'insurance_annual_fee' => $insurance->annual_fee,
                    'monthly_amortization' => $data['monthly_amortization']
                ]);

                // Save Documents
                if (isset($data['client_documents']) && is_array($data['client_documents'])) {
                    foreach ($data['client_documents'] as $doc) {
                        if (
                            empty($doc['document_type_id']) ||
                            empty($doc['file_upload']) ||
                            !($doc['file_upload'] instanceof \Illuminate\Http\UploadedFile)
                        ) {
                            continue;
                        }

                        $path = $doc['file_upload']->store('client_uploads', 'public');

                        ClientDocument::create([
                            'client_id' => $data['borrower_id'],
                            'loan_id' => $loan->id,
                            'document_type_id' => $doc['document_type_id'],
                            'file_path' => $path,
                            'remarks' => $doc['file_upload_remarks'] ?? '',
                        ]);
                    }
                }

            });

            Session::flash('message','Loan created successfully');
            return response()->json([
                'status' => 'success',
                'message' => 'Loan created successfully',
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate entry'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $loan = Loan::with('loanWithdrawRequests')->where('id', $id)->firstOrFail();

        $clients = Client::all();
        $loan_products = LoanProduct::active()->get();

        // check if loan has withdraw request
        $isForWithdraw = $loan->loanWithdrawRequests()
                                ->where('status', 'pending')
                                ->exists();

        $docTypes = DocumentType::active()->get(['id','type_name','description']);
        return view('loan.show', compact('loan', 'clients', 'loan_products', 'isForWithdraw', 'docTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        if ($loan->status == LoanStatus::APPROVE->value)
            abort(404);

        $clients = Client::all();
        $loan_products = LoanProduct::active()->get();

        $docTypes = DocumentType::active()->get(['id','type_name','description']);

        return view('loan.edit', compact('loan', 'clients', 'loan_products','docTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {

        $request->validate([
            'loan_date' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request, $loan) {

                $request->merge([
                    'applied_amount' => str_replace(',', '', $request->applied_amount),
                    'principal' => str_replace(',', '', $request->applied_amount),
                    'advance_interest' => str_replace(',', '', $request->advance_interest),
                    'service_fee' => str_replace(',', '', $request->service_fee),
                    'insurance_fee' => str_replace(',', '', $request->insurance_fee),
                    'notary_fee' => str_replace(',', '', $request->notary_fee),
                    'membership_fee' => str_replace(',', '', $request->membership_fee),
                    'redeem_charges' => str_replace(',', '', $request->redeem_charges),
                    'principal_advance' => str_replace(',', '', $request->principal_advance),
                    'paid_interest_advance' => str_replace(',', '', $request->paid_interest_advance),
                    'monthly_amortization' => str_replace(',', '', $request->monthly_amortization),
                    // 'loan_no' => Loan::generateLoanNumber($request->loan_date) // Don't regenerate loan no on edit
                ]);

                $loan->loan_date = $request->loan_date;
                $loan->borrower_id = $request->borrower_id;
                $loan->loan_product_id = $request->loan_product_id;
                $loan->loan_duration = $request->loan_duration;
                $loan->interest_rate = $request->interest_rate;
                $loan->principal = $request->applied_amount;
                $loan->applied_amount = $request->applied_amount;

                $loan->loanCharges->advance_interest = $request->advance_interest;
                $loan->loanCharges->service_fee = $request->service_fee;
                $loan->loanCharges->insurance_fee = $request->insurance_fee;
                $loan->loanCharges->notary_fee = $request->notary_fee;
                $loan->loanCharges->membership_fee = config('settings.membership_fee');
                $loan->loanCharges->redeem_charges = $request->redeem_charges;
                $loan->loanCharges->principal_advance = $request->principal_advance;
                $loan->loanCharges->membership_fee_paid = $request->membership_fee;
                $loan->loanCharges->interest_advance = $request->interest_advance;
                $loan->loanCharges->paid_interest_advance = $request->paid_interest_advance;
                $loan->loanCharges->save();

                $insurance = InsuranceFeeSchedule::where('min_amount', '<=', $request->applied_amount)
                    ->where('max_amount', '>=', $request->applied_amount)->first();


                $loan->loanFees->insurance_monthly_fee = $insurance->monthly_fee;
                $loan->loanFees->insurance_annual_fee = $insurance->annual_fee;
                $loan->loanFees->monthly_amortization = $request->monthly_amortization;
                $loan->loanFees->save();

                $loan->save();

                // Sync Co-makers
                $borrower = Client::findOrFail($request->borrower_id);

                $newCoMakers = $request->client_comaker ?? [];

                // Get existing co-makers for this specific loan
                $currentCoMakers = DB::table('client_co_makers')
                    ->where('client_id', $borrower->id)
                    ->where('loan_id', $loan->id)
                    ->pluck('co_maker_id')
                    ->toArray();

                // Determine what to remove (in current but not in new request)
                $toRemove = array_diff($currentCoMakers, $newCoMakers);

                // Determine what to add (in new request but not in current)
                $toAdd = array_diff($newCoMakers, $currentCoMakers);

                // Detach removed
                if (!empty($toRemove)) {
                    DB::table('client_co_makers')
                        ->where('client_id', $borrower->id)
                        ->where('loan_id', $loan->id)
                        ->whereIn('co_maker_id', $toRemove)
                        ->delete();
                }

                // Attach added
                if (!empty($toAdd)) {
                    $borrower->coMakers()->attach(
                        array_fill_keys($toAdd, ['loan_id' => $loan->id])
                    );
                }

                // Sync Documents (Delete removed ones)
                $existingDocIds = $request->existing_document_ids ?? [];

                // Get current documents for this loan
                $currentDocuments = ClientDocument::where('loan_id', $loan->id)->get();

                foreach ($currentDocuments as $doc) {
                    if (!in_array($doc->id, $existingDocIds)) {
                        // Optimally delete file from storage here: Storage::disk('public')->delete($doc->file_path);
                        $doc->delete();
                    }
                }

                // Update Existing Documents
                if ($request->has('updated_documents') && is_array($request->updated_documents)) {
                    foreach ($request->updated_documents as $doc) {
                        if (empty($doc['id'])) {
                            continue;
                        }

                        $existingDoc = ClientDocument::find($doc['id']);

                        if (!$existingDoc) continue;

                        // Update fields
                        $existingDoc->document_type_id = $doc['document_type_id'] ?? $existingDoc->document_type_id;
                        $existingDoc->remarks = $doc['file_upload_remarks'] ?? $existingDoc->remarks;
                        $existingDoc->save();
                    }
                }

                // Save New Documents
                if ($request->has('client_documents') && is_array($request->client_documents)) {
                    foreach ($request->client_documents as $doc) {
                        if (
                            empty($doc['document_type_id']) ||
                            empty($doc['file_upload']) ||
                            !($doc['file_upload'] instanceof \Illuminate\Http\UploadedFile)
                        ) {
                            continue;
                        }

                        $path = $doc['file_upload']->store('client_uploads', 'public');

                        ClientDocument::create([
                            'client_id' => $request->borrower_id,
                            'loan_id' => $loan->id,
                            'document_type_id' => $doc['document_type_id'],
                            'file_path' => $path,
                            'remarks' => $doc['file_upload_remarks'] ?? '',
                        ]);
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Loan updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        DB::table('loan_charges')->where('loan_id', $loan->id)->delete();
        DB::table('client_co_makers')->where('loan_id', $loan->id)->delete();
        $loan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan deleted successfully',
        ]);
    }

    public function pending(Request $request)
    {
        if ($request->ajax()) {
            $search = isset($request->search['value']) ? $request->search['value'] : null;

            $loans = Loan::with(['loanProduct', 'loanProduct.loanType', 'borrower.details','loanWithdrawRequests'])
                ->where(function ($query) {
                    $query->pending()
                        ->orWhere(function ($q) {
                            $q->where('status', LoanStatus::APPROVE)
                                ->whereHas('loanWithdrawRequests', function($q) {
                                    $q->where('status','pending');
                                });
                        });
                })->filter($search)
                ->orderByDesc('loan_date')
                ->orderByDesc('loan_no');

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $loans->count();
            $loans = $loans->skip($start)
                            ->take($length)
                            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.pending');
    }

    public function awaitingDisbursement(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::approved()->with('loanProduct', 'loanProduct.loanType', 'borrower.details', 'loanCharges', 'loanDisbursementDetails')->filter($search)
                ->whereNull('disbursed_date')
                ->orderByDesc('loan_date')
                ->orderByDesc('loan_no');

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $loans->count();
            $loans = $loans->skip($start)
                            ->take($length)
                            ->get();


            $loans->map(function ($loan) {
                $loan->net_proceeds = $loan->principal - (
                    ($loan->loanCharges->advance_interest ?? 0) +
                    ($loan->loanCharges->service_fee ?? 0) +
                    ($loan->loanCharges->insurance_fee ?? 0) +
                    ($loan->loanCharges->notary_fee ?? 0) +
                    ($loan->loanCharges->reloan_deduction ?? 0) +
                    ($loan->loanCharges->membership_fee_paid ?? 0) +
                    ($loan->loanCharges->principal_advance ?? 0) +
                    ($loan->loanDisbursementDetails->sum('amount') ?? 0)
                );
                return $loan;
            });


            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.awaiting-disbursement');
    }

    public function declined(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::declined()->with('loanProduct', 'loanProduct.loanType', 'borrower.details')->filter($search)
                ->orderByDesc('loan_date')
                ->orderByDesc('loan_no');

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $loans->count();
            $loans = $loans->skip($start)
                            ->take($length)
                            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.declined');
    }

    public function withdrawn(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::withdrawn()->with('loanProduct', 'loanProduct.loanType', 'borrower.details')->filter($search)
                ->orderByDesc('loan_date')
                ->orderByDesc('loan_no');

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $loans->count();
            $loans = $loans->skip($start)
                            ->take($length)
                            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.withdrawn');
    }

    public function closed(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::closed()->with('loanProduct', 'loanProduct.loanType', 'borrower.details')->filter($search);

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $loans->count();
            $loans = $loans->skip($start)
                            ->take($length)
                            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.closed');
    }

    public function withdrawnLoan(Loan $loan)
    {
        if ($loan) {
            if(!empty($loan->reference_loan_no) && $loan->is_reloan) {
                Loan::where('loan_no', $loan->reference_loan_no)->update([
                        'status' => LoanStatus::APPROVE
                    ]);
            }

            $loan->status = LoanStatus::WITHDRAWN;
            $loan->save();

            LoanWithdrawRequest::where([
                'loan_id' => $loan->id,
                'status' => 'pending'
            ])->update([
                'status' => 'completed',
                'updated_by' => Auth::id()
            ]);

            // reversal or delete
            Savings::where('payment_type_reference', $loan->loan_no)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully withdrawn.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function approveLoan(Loan $loan)
    {
        if ($loan) {
            $loan->status = LoanStatus::APPROVE;
            $loan->approved_date = date('Y-m-d H:i:s');
            $loan->approved_by = auth()->user()->id;
            $loan->save();

            // close previous loan if is_reloan
            if ($loan->is_reloan) {
                $this->closeLoan($loan->reference_loan_no, $loan->approved_date, $loan->loan_no);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully approved.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function declineLoan(Loan $loan)
    {
        if ($loan) {
            $loan->status = LoanStatus::DECLINE;
            $loan->declined_date = date('Y-m-d H:i:s');
            $loan->declined_by = auth()->user()->id;
            $loan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully declined.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function requestApproveLoan(Loan $loan)
    {
        if ($loan) {
            $loan->status = LoanStatus::REQUEST_APPROVE;
            $loan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully request for approval.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function disburse(Loan $loan, Request $request)
    {
        if ($loan) {
            $loan->disbursed_date = $request->disbursed_date;
            $loan->disbursed_notes = $request->notes;
            $loan->mode_of_payment = $request->mode_of_payment;
            // $loan->disbursement_reference = $request->transaction_code;
            $loan->disbursed_by = auth()->user()->id;
            $loan->save();

            //add disbursement details data
            LoanDisbursementDetails::insert([
                'loan_id' => $loan->id,
                'processing_branch' => auth()->user()->current_branch_id,
                'amount' => floatval(str_replace(',', '', $request->amount)),
                'disbursed_by' => auth()->user()->id,
                'disburse_date' => $request->disbursed_date,
                'notes' => $request->notes
            ]);

            if ($request->hasFile('attachment')) {

                $file = $request->file('attachment');
                $filename = $file->getClientOriginalName();
                $folder = 'attachments/' . $loan->loan_no;
                $path = $file->storeAs($folder, $filename, 'public');

                Attachment::create([
                    'file_reference' => 'loans',
                    'transaction_no' => $loan->loan_no,
                    'file_name'      => $filename,
                    'file_path'      => $path,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully disbursed.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function printReceipt($id)
    {
        $loan = Loan::with('borrower.details', 'loanCharges')->findOrFail($id);

        // Calculate Net Proceeds
        $net_proceeds = $loan->principal - (
            ($loan->loanCharges->advance_interest ?? 0) +
            ($loan->loanCharges->service_fee ?? 0) +
            ($loan->loanCharges->insurance_fee ?? 0) +
            ($loan->loanCharges->notary_fee ?? 0) +
            ($loan->loanCharges->reloan_deduction ?? 0) +
            ($loan->loanCharges->special_loan_balance ?? 0) +
            ($loan->loanCharges->membership_fee_paid ?? 0) +
            ($loan->loanCharges->principal_advance ?? 0)
        );

        // Get branch name
        $branchName = $loan->borrower->branch ? $loan->borrower->branch->name : 'Unknown';

        // Custom title rules
        if (strcasecmp($branchName, 'Guiuan') === 0) {
            $branchTitle = 'Head Office';
        } else {
            $branchTitle = $branchName . ' Branch';
        }

        return view('loan.receipt', compact('loan', 'branchTitle', 'net_proceeds'));
    }

    public function processReloan($data)
    {
        try {

            $loan_product = LoanProduct::with('prerequisite')->find($data['loan_product_id']);
            if ($loan_product->prerequisite->isNotEmpty()) {
                $principal_loan = Loan::where('borrower_id', $data['borrower_id'])
                    ->where('status', LoanStatus::APPROVE)
                    ->where('is_special', 0)
                    ->first();

                if (!in_array($principal_loan->loan_product_id, $loan_product->prerequisite->pluck('id')->toArray())) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Required prerequisite loan is missing.',
                    ], 400);
                }
            }

            DB::transaction(function () use ($data) {

                $previous_loan = Loan::with('payments', 'loanCharges')->where('loan_no', $data['reference_loan_no'])->first();
                // $previous_loan->closed_date = $data['loan_date'];
                // $previous_loan->status = LoanStatus::CLOSED;
                // $previous_loan->save();

                // reference loans
                // Loan::where('reference_loan_no',$data['reference_loan_no'])
                //     ->update([
                //         'status' => LoanStatus::CLOSED
                //     ]);

                $previous_loan_principal = $previous_loan->final_principal;
                $payments = $previous_loan?->payments->where('transaction_type', 'withdraw');
                $last_payment_date = $payments?->sortByDesc('transaction_date')->value('transaction_date');
                $total_loan_payment = $payments->sum('principal');
                $principal_balance = $previous_loan_principal - $total_loan_payment;
                $advance_interest = $previous_loan->loanCharges->advance_interest;
                $reloan_deduction = 0;
                $unpaid_interest = 0;
                $special_loan_balance = 0;

                $reloan_date = $data['loan_date'];
                Log::info('last_payment_date:' . $last_payment_date);
                Log::info('reloan_date:' . $reloan_date);
                if ($reloan_date == $last_payment_date) { # no interest due
                    $reloan_deduction = $principal_balance - $advance_interest;
                    $rebate = -$advance_interest;
                } else { # calculate interest
                    $unpaid_interest = 0;

                    $start = Carbon::parse($last_payment_date)->addDay();
                    $end = Carbon::parse($reloan_date);
                    $number_of_days = (int) $start->diffInDays($end);
                    Log::info($start);
                    Log::info($number_of_days);

                    $interest_rate = $previous_loan->interest_rate / 100;
                    $interest = ($interest_rate / 30) * $number_of_days * $principal_balance;
                    $unpaid_interest = round($interest);

                    $reloan_deduction = $principal_balance - ($advance_interest - $unpaid_interest);
                    $rebate = $unpaid_interest - $advance_interest;
                }

                Log::info('principal_balance: ' . $principal_balance);
                Log::info('advance_interest: ' . $advance_interest);
                Log::info('unpaid_interest: ' . $unpaid_interest);
                Log::info('interest(rebate): ' . $rebate);
                Log::info('reloan_deduction: ' . $reloan_deduction);

                // temp payment reference
                $temp_reference = 'tmp'.time();

                if ($data['include_special_loan_balance']) {
                    $special_loan_balance = $data['special_loan_balance'];
                    // create saving for payment
                    Savings::create([
                        'transaction_date' => $data['loan_date'],
                        'transaction_type' => 'deposit',
                        'client_id' => $data['borrower_id'],
                        'amount' => $principal_balance + $special_loan_balance,
                        'payment_type' => PaymentType::RELOAN_DEDUCTION,
                        'payment_type_reference' => $temp_reference,
                        'description' => 'Reloan',
                        'notes' => 'Reloan',
                    ]);

                    $client = Client::findOrFail($data['borrower_id']);
                    $loans = $client->getApproveLoans();



                    foreach ($loans as $loan) {
                        $payments = $loan?->payments->where('transaction_type', 'withdraw');

                        $loan_principal_balance = $loan->final_principal;
                        $loan_principal_payments = $payments->sum('principal');
                        $early_settlement_amount = $loan_principal_balance - $loan_principal_payments;

                        Log::info('principal:' . $loan->final_principal);
                        Log::info('payment:' . $payments->sum('principal'));
                        Log::info('early_settlement_amount' . $early_settlement_amount);
                        Log::info('----------------------------------------------------------');

                        if ($loan->is_special) {
                            Savings::create([
                                'transaction_date' => $data['loan_date'],
                                'transaction_type' => 'withdraw',
                                'client_id' => $data['borrower_id'],
                                'loan_id' => $loan->id,
                                'amount' => $early_settlement_amount,
                                'interest' => 0,
                                'principal' => $early_settlement_amount,
                                'payment_type' => PaymentType::RELOAN_DEDUCTION,
                                'payment_type_reference' => $temp_reference,
                                'description' => 'Reloan',
                                'notes' => 'Reloan',
                            ]);
                        } else {
                            $principal_amount = $reloan_deduction - $special_loan_balance;

                            Savings::create([
                                'transaction_date' => $data['loan_date'],
                                'transaction_type' => 'withdraw',
                                'client_id' => $data['borrower_id'],
                                'loan_id' => $previous_loan->id,
                                'amount' => $principal_balance,
                                'interest' => $rebate,
                                'principal' => $principal_amount - $rebate,
                                'payment_type' => PaymentType::RELOAN_DEDUCTION,
                                'payment_type_reference' => $temp_reference,
                                'description' => 'Reloan',
                                'notes' => 'Reloan',
                            ]);
                        }
                    }

                } else {

                    // create saving for payment
                    Savings::create([
                        'transaction_date' => $data['loan_date'],
                        'transaction_type' => 'deposit',
                        'client_id' => $data['borrower_id'],
                        'amount' => $principal_balance,
                        'payment_type' => PaymentType::RELOAN_DEDUCTION,
                        'payment_type_reference' => $temp_reference,
                        'description' => 'Reloan',
                        'notes' => 'Reloan',
                    ]);

                    Savings::create([
                        'transaction_date' => $data['loan_date'],
                        'transaction_type' => 'withdraw',
                        'client_id' => $data['borrower_id'],
                        'loan_id' => $previous_loan->id,
                        'amount' => $principal_balance,
                        'interest' => $rebate,
                        'principal' => $reloan_deduction - $rebate,
                        'payment_type' => PaymentType::RELOAN_DEDUCTION,
                        'payment_type_reference' => $temp_reference,
                        'description' => 'Reloan',
                        'notes' => 'Reloan',
                    ]);
                }

                $loan = Loan::create($data);

                LoanCharge::create([
                    'loan_id' => $loan->id,
                    'advance_interest' => $data['advance_interest'],
                    'service_fee' => $data['service_fee'],
                    'insurance_fee' => $data['insurance_fee'],
                    'notary_fee' => $data['notary_fee'],
                    'special_loan_balance' => $special_loan_balance,
                    'reloan_deduction' => $reloan_deduction,
                ]);

                $insurance = InsuranceFeeSchedule::where('min_amount', '<=', $data['applied_amount'])
                    ->where('max_amount', '>=', $data['applied_amount'])->first();

                LoanFee::create([
                    'loan_id' => $loan->id,
                    'insurance_monthly_fee' => $insurance->monthly_fee,
                    'insurance_annual_fee' => $insurance->annual_fee,
                    'monthly_amortization' => $data['monthly_amortization']
                ]);

                // update temp reference to new loan no
                Savings::where('payment_type_reference', $temp_reference) ->update([
                    'payment_type_reference' => $loan->loan_no
                ]);

            });

            Session::flash('message','Loan created successfully');
            return response()->json([
                'status' => 'success',
                'message' => 'Loan created successfully',
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate entry'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.' . $e->getMessage()
            ], 500);
        }
    }

    public function processAdditionalLoan($data)
    {
        try {

            DB::transaction(function () use ($data) {

                $previous_loan = Loan::with('payments', 'loanCharges')->where('loan_no', $data['reference_loan_no'])->first();

                $previous_loan_principal = $previous_loan->principal;
                $payments = $previous_loan?->payments->where('transaction_type', 'withdraw');
                $total_loan_payment = $payments->sum('principal');
                $principal_balance = $previous_loan_principal - $total_loan_payment;

                $loan = Loan::create($data);

                LoanCharge::create([
                    'loan_id' => $loan->id,
                    'advance_interest' => $data['advance_interest'],
                    'service_fee' => $data['service_fee'],
                    'insurance_fee' => $data['insurance_fee'] == "" ? 0 : $data['insurance_fee'],
                    'notary_fee' => $data['notary_fee'],
                    'reloan_deduction' => 0,
                ]);

                LoanFee::create([
                    'loan_id' => $loan->id,
                    'insurance_monthly_fee' => 0,
                    'insurance_annual_fee' => 0,
                    'monthly_amortization' => $data['monthly_amortization']
                ]);

            });

            Session::flash('message','Loan created successfully');
            return response()->json([
                'status' => 'success',
                'message' => 'Loan created successfully',
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate entry'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.' . $e->getMessage()
            ], 500);
        }
    }

    public function getReloanDeduction(Request $request)
    {
        if ($request->ajax()) {
            $loan = Loan::with('borrower.details', 'payments', 'loanCharges')->where('loan_no', $request->reference_loan_no)->first();
            $reloan_date = $request->reloan_date;
            $amount = $this->calculateReloanDeduction($loan, $reloan_date);
            $reloan_deduction = $amount['reloan_deduction'];
            $principal_loan_balance = $amount['principal_loan_balance'];
            $special_loan_balance = $amount['special_loan_balance'];

            return response()->json([
                'amount' => $reloan_deduction,
                'principal_loan_balance' => $principal_loan_balance,
                'special_loan_balance' => $special_loan_balance,
            ]);
        }
    }

    function calculateReloanDeduction(Loan $previous_loan, $reloan_date)
    {
        $previous_loan_principal = $previous_loan?->final_principal;
        $payments = $previous_loan?->payments->where('transaction_type', 'withdraw');
        $last_payment_date = $payments?->sortByDesc('transaction_date')->value('transaction_date');
        $total_loan_payment = $payments->sum('principal');
        Log::info('calculate reloan deduction');
        Log::info('previous_loan_principal:' . $previous_loan_principal);
        Log::info('total_loan_payment:' . $total_loan_payment);
        $principal_balance = $previous_loan_principal - $total_loan_payment;
        Log::info('principal_balance:' . $principal_balance);
        $advance_interest = (float) $previous_loan?->final_advance_interest;
        $reloan_deduction = 0;
        $unpaid_interest = 0;

        $end = Carbon::parse($reloan_date);
        $previous_loan_date = Carbon::parse($previous_loan->loan_date);
        $number_of_months = abs((int) $previous_loan_date->diffInMonths($end)) + 1;
        Log::info(Str::slug($previous_loan->loanProduct->loanType->name));

        $unpaid_insurance = $number_of_months >= 12 ? $previous_loan->final_insurance_monthly * ($number_of_months - 12) : 0;

        $special_loan_balance = getSpecialLoanBalance($previous_loan->borrower_id);
        if (Carbon::parse($reloan_date)->format('Y-m-d') == Carbon::parse($last_payment_date ?? $previous_loan->disbursed_date)->format('Y-m-d')) {
            Log::info('-----------------');
            Log::info(Carbon::parse($reloan_date)->format('Y-m-d'));
            Log::info(Carbon::parse($last_payment_date ?? $previous_loan->disbursed_date)->format('Y-m-d'));
            Log::info('-----------------');
            // No interest due
            $reloan_deduction = $principal_balance - $advance_interest + $special_loan_balance;

            if (Str::slug($previous_loan->loanProduct->loanType->name) == 'open-term') {
                $reloan_deduction = $principal_balance - $advance_interest - $unpaid_insurance + $special_loan_balance;
            }
        } else {
            // Calculate interest
            $start = Carbon::parse($last_payment_date)->addDay();

            if ($start->gt($end)) {
                $number_of_days = 0;
                // return response()->json([
                //     'status' => 'failed',
                //     'message' => 'Reloan date is after/on last payment date',
                // ],422);
            } else {
                $number_of_days = abs((int) $start->diffInDays($end)) + 1;
            }

            Log::info('start:' . $start);
            Log::info('end:' . $end);
            Log::info($number_of_days);

            $interest_rate = $previous_loan->interest_rate / 100;
            $interest = ($interest_rate / 30) * $number_of_days * $principal_balance;
            $unpaid_interest = round($interest);

            if (Str::slug($previous_loan->loanProduct->loanType->name) == 'open-term' || Str::slug($previous_loan->loanProduct->loanType->name) == 'auto-debit') {
                $reloan_deduction = $principal_balance - ($advance_interest - $unpaid_interest) + $unpaid_insurance + $special_loan_balance;
                Log::info($reloan_deduction . '=' . $principal_balance . '-(' . $advance_interest . '-' . $unpaid_interest . ') + ' . $unpaid_insurance . '+' . $special_loan_balance);
            } else {
                $reloan_deduction = $principal_balance + $unpaid_insurance + $special_loan_balance;
                Log::info($reloan_deduction . '=' . $principal_balance . '+' . $unpaid_insurance . '+' . $special_loan_balance);
            }
        }



        Log::info('principal_balance: ' . $principal_balance);
        Log::info('principal_balance: ' . $principal_balance);
        Log::info('total_loan_payment: ' . $total_loan_payment);
        Log::info('advance_interest: ' . $advance_interest);
        Log::info('unpaid_interest: ' . $unpaid_interest);
        Log::info('unpaid_insurance: ' . $unpaid_insurance);
        Log::info('special_loan_balance: ' . $special_loan_balance);
        Log::info('reloan_deduction: ' . $reloan_deduction);

        return [
            'reloan_deduction' => number_format($reloan_deduction, 2),
            'principal_loan_balance' => number_format($reloan_deduction - $special_loan_balance, 2),
            'special_loan_balance' => number_format($special_loan_balance, 2)
        ];
    }

    function closeLoan($reference_loan, $closed_date, $current_loan_no)
    {
        $previous_loan = Loan::where('loan_no', $reference_loan)->first();
        $previous_loan->closed_date = $closed_date;
        $previous_loan->status = LoanStatus::CLOSED;
        $previous_loan->save();

        // reference loans related to previous loan
        Loan::where('reference_loan_no', $reference_loan)
            ->where('loan_no', '!=', $current_loan_no)
            ->update([
                'status' => LoanStatus::CLOSED,
                'closed_date' => $closed_date
            ]);
    }

    public function requestLoanWithraw(int $id)
    {
        $loan = Loan::where('id', $id)->first();

        if ($loan) {

            $exists = LoanWithdrawRequest::where('loan_id', $id)
                ->whereNot('status', 'cancelled') // optional condition
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Request already exists',
                ]);
            }

            try {
                LoanWithdrawRequest::create([
                    'loan_id' => $id,
                    'status' => 'pending',
                    'requested_by' => Auth::id(),
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Request already exists. ' . $e->getMessage(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Request submitted successfully',
            ]);
        }


    }

    public function updateDisbursementReference(Request $request, int $id)
    {
        $loan = Loan::where('id', $id)->first();
        $loan->disbursement_reference = $request->transaction_code;
        $loan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction code updated successfully',
        ]);
    }

    public function previewSoaTerm(int $id)
    {
        return view('loan.report.soa-term');
    }

    public function downloadSoaTerm(int $id)
    {
        $pdf = Pdf::loadView('loan.report.soa-term-pdf')->setPaper('a4', 'landscape');

        return $pdf->download(Str::random(40) . '.pdf');
    }

    public function previewSoaNoTerm(int $id)
    {
        return view('loan.report.soa-no-term');
    }

    public function downloadSoaNoTerm(int $id)
    {
        $pdf = Pdf::loadView('loan.report.soa-no-term-pdf')->setPaper('legal', 'landscape');

        return $pdf->download(Str::random(40) . '.pdf');
    }


}
