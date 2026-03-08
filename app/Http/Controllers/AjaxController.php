<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Savings;
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
}
