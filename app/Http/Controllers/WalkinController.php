<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

//reference no service
use App\Services\ReferenceNumberService;   

//models
use App\Models\Walkin;

class WalkinController extends Controller
{

    public function index()
    {
        $serviceCharge = DB::table('service_charges')
        ->where('is_active', 1)
        ->where('fee_type', 'walkin')   
        ->orderByDesc('id')             
        ->value('percentage') ?? 0;

         // Fetch existing walkins
        $walkins = Walkin::orderBy('date', 'desc')->get();
        //  dd($serviceCharge);

             // 'date', 'desc')->get();
        return view('others.walkin.index', compact('walkins', 'serviceCharge'));
    }

    public function store(Request $request)
    {
        $response = [];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_initial' => 'nullable|string|max:5',
            'amount_withdrawn' => 'required|numeric',
            'charge' => 'nullable|numeric',
            'net_proceeds' => 'required|numeric',
            'no_service_charge' => 'nullable|numeric',
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'fail',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        try {
            DB::beginTransaction();

            $data = $request->except('id');

            //insert processing branch
            $data['branch_id'] = auth()->user()->current_branch_id;

            $referenceNumberService = new ReferenceNumberService();

            $referenceNumber = $referenceNumberService->generateReferenceNumber(Walkin::class, auth()->user()->current_branch_id);
            $data['reference_no'] = $referenceNumber . 'W';
            $result= Walkin::create($data);

            DB::commit();
            $response = [
                'status' => 'success',
                'message' => 'Walk-in created successfully.',
                'data' => [
                    'id' => $result->id
                ]
            ];

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error creating walk-in record: ' . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Failed to create walk-in record. Please try again later.'
            ];
        }

        return response()->json($response);
    }

    public function show($id)
    {
        $response = [];

        $walkin = Walkin::find($id);
        if (!$walkin) {
            $response = [
                'status' => 'error',
                'message' => 'Walk-in data not found.',
            ];
        }else{
            $response = [
                'status' => 'success',
                'message' => 'Walk-in data retrieved successfully.',
                'data' => [
                    'id' => $walkin->id,
                    'branch_id' => $walkin->branch_id,
                    'date' => $walkin->date->format('Y-m-d'),
                    'last_name' => $walkin->last_name,
                    'first_name' => $walkin->first_name,
                    'middle_initial' => $walkin->middle_initial,
                    'amount_withdrawn' => $walkin->amount_withdrawn,
                    'charge' => $walkin->charge,
                    'net_proceeds' => $walkin->net_proceeds,
                    'no_service_charge' => $walkin->no_service_charge,
                ]
            ];
        }

        return response()->json($response);
    }


    public function printReceipt($id)
    {
        $walkin = Walkin::findOrFail($id);

        $user = auth()->user();
        $branchName = $user?->branch?->name ?? 'Unknown';

        $branchTitle = strcasecmp($branchName, 'Guiuan') === 0
            ? 'Head Office'
            : $branchName . ' Branch';

        // return view('others.walkin.receipt', compact('walkin', 'branchTitle'));
        $pdf = Pdf::loadView('others.walkin.receipt2', compact(
            'walkin',
            'branchTitle',
        ))
        ->setPaper([0, 0, 226.77, 600], 'portrait');

        return $pdf->stream('receipt.pdf');
    }

    public function update(Request $request)
    {
        $response = [];

        $walkinData = Walkin::find($request->id);
        if (!$walkinData) {
            $response = [
                'status' => 'error',
                'message' => 'Walk-in data not found.',
            ];
        }else{

            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'last_name' => 'required|string|max:100',
                'first_name' => 'required|string|max:100',
                'middle_initial' => 'nullable|string|max:5',
                'amount_withdrawn' => 'required|numeric',
                'charge' => 'nullable|numeric',
                'net_proceeds' => 'required|numeric',
                'no_service_charge' => 'nullable|numeric',
            ]);

            if($validator->fails()){
                $allErrors = implode(',', $validator->errors()->all());
                $response = [
                    'status' => 'fail',
                    'message' => 'Validation Failed!: ' . $allErrors,
                ];

                return response()->json($response);
            }
            try {
                DB::beginTransaction();

                $data = $request->all();

                if(!isset($data['charge'])) {
                    $data['charge'] = '0.00';
                }

                $walkinData->update($data);
                DB::commit();

                $response = [
                    'status' => 'success',
                    'message' => 'Walk-in updated successfully.'
                ];

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Error updating walk-in record: ' . $e->getMessage());
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to update walk-in record. Please try again later.'
                ];
            }
        }

        return response()->json($response);
    }


    public function destroy($id)
    {
        $response = [];
        $walkin = Walkin::find($id);
        if (!$walkin) {
            $response = [
                'status' => 'error',
                'message' => 'Walk-in not found.'
            ];
        }

        try {
            DB::beginTransaction();
            $walkin->delete();
            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Walk-in deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error deleting walk-in record: ' . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Failed to delete walk-in record. Please try again later.'
            ];
        }

        return response()->json($response);
    }
}
