<?php

namespace App\Http\Livewire\Admin\Transcript;

use App\Models\Result;
use App\Models\Students;
use Illuminate\Contracts\Validation\Validator;
use Livewire\Component;

class HNDResults extends Component
{
    public $name = "";
    public $year = "";
    public $grade = "";

    public ?Result $result;

    public Students $student;

    protected $listeners = [
        "updated"=>'$refresh'
    ];

    protected function getRules()
    {
        return [
            "year" => 'required',
            'grade' =>'required',
        ];
    }


    public function mount(Students $student)
    {
        $this->student = $student;
        $this->name = $student->name;

        $this->result = Result::where([
            'semester_id'=>'HND',
            'student_id'=>$student->id
        ])->first();

        if (isset($this->result)){
            $this->year = $this->result->batch_id;
            $this->grade = $this->result->exam_score;
        }

    }

    public function save()
    {
        $data = $this->withValidator(function (\Illuminate\Validation\Validator $validator) {
            $validator->after(function (Validator $validator) {
            });
        })->validate();

        if(isset($this->result)){
            $this->result->update([
                'semester_id'=>'HND',
                'class_id'=>'HND',
                'exam_score'=>$this->grade,
                'batch_id'=>$this->year,
                'student_id'=>$this->student->id
            ]);
        }else{
           $this->result =  Result::create([
                'semester_id'=>'HND',
                'class_id'=>'HND',
                'exam_score'=>$this->grade,
                'batch_id'=>$this->year,
                'student_id'=>$this->student->id
            ]);
        }

        $this->redirectRoute('admin.transcript.index');

    }


    public function delete()
    {
        $this->result->delete();
        $this->result = null;
        $this->emit('updated');

    }



    public function render()
    {
        return view('livewire.admin.transcript.h-n-d-results')->layout('admin.layout');
    }
}
