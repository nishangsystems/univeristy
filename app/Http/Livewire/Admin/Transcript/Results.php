<?php

namespace App\Http\Livewire\Admin\Transcript;

use App\Helpers\Helpers;
use App\Models\Semester;
use App\Models\Students;
use App\Models\Subjects;
use Livewire\Component;
use Illuminate\Http\Request;

class Results extends Component
{

    public Students $student;

    public $gradings = [];

    public $totalCreditAttempted = 0;

    public $totalCreditEarned = 0;

    public $results = [];

    public $gpa = 0.23;

    public function render()
    {
        return view('livewire.admin.transcript.results')->layout('livewire.admin.transcript.layout');
    }

    public function mount(Request $request, Students $student){
        $this->student = $student;

        $class = $student->_class();

        $this->gradings = $class->program()->first()->gradingType->grading()->orderBy('weight','desc')->get() ?? [];
        $prog = $class->program;

        $this->results = $student->result->map(function ($result)  use ($prog){
            foreach ($this->gradings as $key => $grade) {
                $total = (($result->ca_score ?? 0 )+ ($result->exam_score ?? 0));
                $passed = $total >= ($prog->ca_total + $prog->exam_total)*0.5;
                if ($total >= $grade->lower && $total <= $grade->upper) {
                    $this->totalCreditAttempted += $result->subject->coef;
                    $this->totalCreditEarned += $passed ? $result->subject->coef:0;
                    return collect([
                        'name'=>$result->subject->name,
                        'year_name'=>$result->year->name,
                        'semester_name'=>$result->semester->name,
                        'semester_id'=>$result->semester_id,
                        'batch_id'=>$result->batch_id,
                        'code'=>$result->subject->code,
                        'type'=>$result->subject->status,
                        'cv'=>$result->subject->coef,
                        'ce'=>$passed ? $result->subject->coef:0,
                        'grade'=> ($grade != "")?$grade->grade:"-",
                        'gp'=>($grade != "")?$grade->weight:0.0,
                    ]);
                }
            }
        });

        $this->gpa = Helpers::getGPA($this->results);

        $this->results =    $this->results->groupBy('batch_id');




    }
}
