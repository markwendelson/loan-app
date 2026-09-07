<?php

namespace App\Livewire\Database;

use Livewire\Component;
use Livewire\Attributes\On;

class DatabaseFilter extends Component
{
    public $entries = 10;

    public function updated()
    {
        $this->dispatch('refresh-database-data', [
            'entries' => $this->entries
        ]);
    }

    public function render()
    {
        return view('livewire.database.database-filter');
    }
}
