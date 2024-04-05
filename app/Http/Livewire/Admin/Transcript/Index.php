<?php

namespace App\Http\Livewire\Admin\Transcript;

use Livewire\Component;

class Index extends Component
{

    public function render()
    {
        return view('livewire.admin.transcript.index')->layout('admin.layout',['title' => "Print Transcript"]);
    }
}
