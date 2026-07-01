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
            'no_service_charge' => 'nullable|boolean',
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'fail',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        if ($request->no_service_charge) {
            $validated['charge'] = 0;
            $validated['net_proceeds'] = $validated['amount_withdrawn'];

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
        $walkin = Walkin::find($id);
        if (!$walkin) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        // Format date for modal (YYYY-MM-DD)
        $walkin->date = $walkin->date->format('Y-m-d');

           $walkin = Walkin::findOrFail($id);

    return response()->json([
        'id' => $walkin->id,
        'date' => $walkin->date->format('Y-m-d'), // <- safe for <input type="date">
        'last_name' => $walkin->last_name,
        'first_name' => $walkin->first_name,
        'middle_initial' => $walkin->middle_initial,
        'amount_withdrawn' => $walkin->amount_withdrawn,
        'charge' => $walkin->charge,
        'net_proceeds' => $walkin->net_proceeds,
        'no_service_charge' => $walkin->no_service_charge,
    ]);


    }


    public function printReceipt($id)
    {
         $walkin = Walkin::findOrFail($id);

         $user = auth()->user();
         $branchName = $user?->branch?->name ?? 'Unknown';

         $branchTitle = strcasecmp($branchName, 'Guiuan') === 0
             ? 'Head Office'
             : $branchName . ' Branch';

         return view('others.walkin.receipt', compact('walkin', 'branchTitle'));
    }

   
    public function update(Request $request, $id)
    {
        $walkin = Walkin::find($id);
        if (!$walkin) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_initial' => 'nullable|string|max:5',
            'amount_withdrawn' => 'required|numeric',
            'charge' => 'nullable|numeric',
            'net_proceeds' => 'required|numeric',
            'no_service_charge' => 'nullable|in:0,1',
        ]);

        if ($request->boolean('no_service_charge')) {
            $validated['charge'] = 0;
            $validated['net_proceeds'] = $validated['amount_withdrawn'];
        }
        $walkin->update($validated);

        return response()->json([
            'data' => $walkin,
            'print_url' => route('walkin.receipt', $walkin->id),
        ]);

    }


    public function destroy($id)
    {
        $walkin = Walkin::find($id);
        if (!$walkin) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $walkin->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}
