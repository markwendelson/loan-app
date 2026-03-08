<?php

namespace App\Http\Controllers;

use App\Models\Client;
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

    public function addClientIncentives(Request $request, Client $client)
    {
        try {
            $data = $request->all();

            Incentives::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Incentives added successfully',
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
}
