<?php

namespace App\Livewire\CheckEncashment;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

//model
use App\Models\CheckEncashment;

class EncashmentData extends Component
{
    use WithPagination;

    public $q;
    public $entries = 10;
    public $bank;

    #[On('encashment-filter-updated')]
    public function encashmentFilterUpdated($data)
    {
        $this->q = $data['q'];
        $this->entries = $data['entries'];
        $this->bank = $data['bank'];
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th class="text-center" width="120">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th width="50" class="text-center">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for ($x = 1; $x < 10; $x++)
                        <tr>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                        </tr>
                    @endfor
                </tbody>

            </table>
        </div>
        HTML;
    }

    public function render()
    {
        $query = CheckEncashment::select(
            'check_encashment.id',
            'check_encashment.reference_no',
            'check_encashment.transDate',
            'check_encashment.last_name',
            'check_encashment.first_name',
            'check_encashment.middle_name',
            'check_encashment.checkNo',
            'check_encashment.checkAmount',
            'check_encashment.charge',
            'check_encashment.net_proceeds',
            'check_encashment.id_picture_path',
            'bank.name as bank_name',
            'bank.bank_code'
        )
        ->leftJoin('bank', 'bank.id', '=', 'check_encashment.bank_id')
        ->when($this->q, function($query){
            $query->where('check_encashment.last_name', 'like', '%'. $this->q .'%')
                ->orWhere('check_encashment.first_name', 'like', '%'. $this->q .'%')
                ->orWhere('check_encashment.middle_name', 'like', '%'. $this->q .'%')
                ->orWhere('check_encashment.checkNo', $this->q);
        })
        ->when($this->bank, function($query) {
            $query->where('bank.bank_code', $this->bank);
        });
        $query->orderBy('transDate', 'DESC');

        $checks = $query->paginate($this->entries)
            ->appends([
                'q' => $this->q,
                'entries' => $this->entries,
                'bank' => $this->bank
            ]);

        return view('livewire.check-encashment.encashment-data', compact(
            'checks'
        ));
    }
}
