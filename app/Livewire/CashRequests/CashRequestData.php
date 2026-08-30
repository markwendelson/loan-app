<?php

namespace App\Livewire\CashRequests;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\CashRequest;

class CashRequestData extends Component
{
    use WithPagination;

    public $entries = 10;
    public $branch_id;
    public $status;

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
    }

    #[On('cash-request-filter-updated')]
    public function filterUpdated($data)
    {
        $this->entries = $data['entries'];
        $this->branch_id = $data['branch_id'];
        $this->status = $data['status'];
        $this->resetPage();
    }

    #[On('refresh-cash-request-data')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        HTML;
    }

    public function render()
    {
        $query = CashRequest::with(['employee', 'branch', 'approver'])
            ->orderBy('created_at', 'desc');

        if ($this->branch_id && $this->branch_id !== 'all') {
            $query->where('branch_id', $this->branch_id);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $cashRequests = $query->paginate($this->entries)->appends([
            'entries' => $this->entries,
            'branch_id' => $this->branch_id,
            'status' => $this->status,
        ]);

        return view('livewire.cash-requests.cash-request-data', compact('cashRequests'));
    }
}
