<?php

namespace App\Livewire\Client\ClientList\ColorCode;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

//models
use App\Models\ClientColorCode;

class ColorCodeData extends Component
{
    public $entries = 10;

    #[On("refresh-color-code")]
    public function refreshColorCode()
    {

    }

    public function mount()
    {
    }

    public function render()
    {
        $colorCodes = ClientColorCode::where('status', 1)->paginate($this->entries);

        return view('livewire.client.client-list.color-code.color-code-data', compact(
            'colorCodes'
        ));
    }

    public function removeColorCode($id)
    {
        try {
            DB::beginTransaction();

            $colorCode = ClientColorCode::find($id)->delete();

            $this->dispatch("refresh-client-list");
            $this->dispatch("refresh-color-code");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
}
