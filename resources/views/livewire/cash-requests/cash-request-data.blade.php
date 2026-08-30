<div>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped" id="basic-1">
            <thead class="bg-default">
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Branch</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Approver</th>
                    @canany(['approve-cash-request', 'edit-cash-request', 'delete-cash-request'])
                    <th class="text-center">Action</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse($cashRequests as $request)
                <tr>
                    <td>{{ $request->created_at->format('Y-m-d h:i A') }}</td>
                    <td>{{ $request->employee ? $request->employee->display_name : 'N/A' }}</td>
                    <td>{{ $request->branch ? $request->branch->name : 'N/A' }}</td>
                    <td class="text-end">{{ number_format($request->amount, 2) }}</td>
                    <td>
                        @if($request->status == 'pending')
                            <span class="badge badge-warning text-dark">Pending</span>
                        @elseif($request->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($request->status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($request->approved_by)
                            {{ $request->approver ? $request->approver->name : 'Unknown' }}<br>
                            <small>{{ $request->approved_at->format('Y-m-d') }}</small>
                        @else
                            -
                        @endif
                    </td>
                    @canany(['approve-cash-request', 'edit-cash-request', 'delete-cash-request'])
                    <td class="text-center">
                        <div class="btn-group">
                            @if($request->status == 'pending')
                                @can('approve-cash-request')
                                <button type="button" class="btn btn-success btn-sm btn-approve" data-id="{{ $request->id }}" title="Approve">
                                    <i class="fa fa-check"></i>
                                </button>
                                @endcan
                                
                                @can('edit-cash-request')
                                <button type="button" class="btn btn-primary btn-sm btn-edit" 
                                    data-id="{{ $request->id }}"
                                    data-employee="{{ $request->employee_id }}"
                                    data-branch="{{ $request->branch_id }}"
                                    data-amount="{{ $request->amount }}"
                                    title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                
                                @can('delete-cash-request')
                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $request->id }}" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                                @endcan
                            @endif
                        </div>

                        <form id="approve-form-{{ $request->id }}" action="{{ route('cash-requests.approve', $request->id) }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <form id="delete-form-{{ $request->id }}" action="{{ route('cash-requests.destroy', $request->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                    @endcanany
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No cash requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-between mt-3">
        <div>
            Showing {{ $cashRequests->firstItem() ?? 0 }} to {{ $cashRequests->lastItem() ?? 0 }} of {{ $cashRequests->total() }} entries
        </div>
        <div>
            {{ $cashRequests->links() }}
        </div>
    </div>
</div>
