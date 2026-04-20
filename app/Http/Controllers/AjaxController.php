<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\DocumentType;
use App\Models\Incentives;
use App\Models\Savings;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function getClients(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'] ?? '';

            $query = Client::query()->with('details');

            // Exclude borrower_id if provided
            if ($request->has('borrower_id') && $request->borrower_id) {
                $query->where('id', '!=', $request->borrower_id);
            }

            // Apply search filter if needed
            if ($search) {
                $query->filter($search);
            }

            // Get total before limiting
            $totalRecords = $query->count();

            // Limit to 3 records
            $clients = $query->limit(3)->get();

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $clients
            ]);
        }
    }

    public function getAvailableMerchantSlipNo(Request $request)
    {
        $withdrawnCodes = Savings::where('client_id', $request->client_id)
                                    ->where('mode_of_payment', $request->mode_of_payment)
                                    ->where('transaction_type', 'withdraw')
                                    ->whereNull('deleted_at')
                                    ->pluck('transaction_code')
                                    ->flatMap(fn($codes) => explode(',', $codes))
                                    ->toArray();

        $data = Savings::where('client_id', $request->client_id)
                        ->where('mode_of_payment', $request->mode_of_payment)
                        ->where('transaction_type', 'deposit')
                        ->whereNotIn('transaction_code', $withdrawnCodes)
                        ->get(['transaction_code','amount']);

        return $data;

    }

    public function clientIncentives(Request $request, Client $client)
    {
        $incentives = DB::table('incentives')->where('client_id', $client->id);

        $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $incentives->count();
            $incentives = $incentives->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw), // Keep track of the request number
            'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
            'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
            'data' => $incentives  // The paginated and formatted data
        ]);
    }

    public function addClientIncentives(Request $request)
    {
        try {
            $data = $request->all();

            $incentives = Incentives::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Incentives added successfully',
                'id' => $incentives->id
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

    public function getDocumentTypes()
    {
        $document_types = DocumentType::active()->get(['id','type_name','description']);

        return $document_types;
    }

    public function uploadAttachment(Request $request)
    {
        try {

            $loan_id = $request->loan_id;
            $client_id = $request->client_id;
            $document_type_id = $request->doc_type;
            $remarks = $request->remarks ?? '';

            if ($request->hasFile('file_upload')) {

                $file = $request->file('file_upload');
                // $filename = $file->getClientOriginalName();
                // $folder = 'attachments/' . $loan_no;
                // $path = $file->storeAs($folder, $filename, 'public');
                $path = $file->store('client_uploads', 'public');


                ClientDocument::create([
                    'client_id' => $client_id,
                    'loan_id' => $loan_id,
                    'document_type_id' => $document_type_id,
                    'file_path' => $path,
                    'remarks' => $remarks,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'File uploaded successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nothing to upload',
                ], 422);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.' . $th->getMessage()
            ], 500);
        }
    }
}
