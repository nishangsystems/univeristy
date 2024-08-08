<?php

namespace App\View\Components;

use App\Models\SchoolUnits;
use Illuminate\View\Component;

class ClassFilter extends Component
{

    protected $campus, $field_name, $data=[];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        //
        $this->data['field_name'] = $data['field_name'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->data['schools'] = SchoolUnits::where('unit_id', 1)->orderBy('name')->get();

        return view('components.class-filter', $this->data);
    }
}
