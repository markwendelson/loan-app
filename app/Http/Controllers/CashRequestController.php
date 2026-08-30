<?php

namespace App\Http\Controllers;

use App\Models\CashRequest;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRequestController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->can('view-cash-request'), 403, 'Unauthorized action.');

        $employees = Employee::where('status', 1)->orderBy('display_name')->get();
        $branches = Branch::where('status', 1)->orderBy('name')->get();

        return view('cash_requests.index', compact('employees', 'branches'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('create-cash-request'), 403, 'Unauthorized action.');

        $request->merge([
            'amount' => str_replace(',', '', $request->amount)
        ]);

        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'branch_id' => 'required|exists:branch,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        CashRequest::create([
            'employee_id' => $request->employee_id,
            'branch_id' => $request->branch_id,
            'amount' => $request->amount,
            'created_at' => now(),
            'status' => 'pending',
        ]);

        return redirect()->route('cash-requests.index')->with('message', 'Cash request submitted successfully.');
    }

    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit-cash-request'), 403, 'Unauthorized action.');

        $cashRequest = CashRequest::findOrFail($id);

        if ($cashRequest->status !== 'pending') {
            return redirect()->route('cash-requests.index')->with('error', 'Only pending requests can be modified.');
        }

        $request->merge([
            'amount' => str_replace(',', '', $request->amount)
        ]);

        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'branch_id' => 'required|exists:branch,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $cashRequest->update([
            'employee_id' => $request->employee_id,
            'branch_id' => $request->branch_id,
            'amount' => $request->amount,
        ]);

        return redirect()->route('cash-requests.index')->with('message', 'Cash request updated successfully.');
    }

    public function destroy($id)
    {
        abort_if(!auth()->user()->can('delete-cash-request'), 403, 'Unauthorized action.');

        $cashRequest = CashRequest::findOrFail($id);
        
        if ($cashRequest->status !== 'pending') {
            return redirect()->route('cash-requests.index')->with('error', 'Only pending requests can be deleted.');
        }

        $cashRequest->delete();

        return redirect()->route('cash-requests.index')->with('message', 'Cash request deleted successfully.');
    }

    public function approve($id)
    {
        abort_if(!auth()->user()->can('approve-cash-request'), 403, 'Unauthorized action.');

        $cashRequest = CashRequest::findOrFail($id);
        
        if ($cashRequest->status !== 'pending') {
            return redirect()->route('cash-requests.index')->with('error', 'Request is already processed.');
        }

        $cashRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('cash-requests.index')->with('message', 'Cash request approved successfully.');
    }
}
