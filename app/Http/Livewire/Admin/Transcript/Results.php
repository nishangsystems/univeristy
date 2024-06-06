<?php

namespace App\Http\Livewire\Admin\Transcript;

use App\Helpers\Helpers;
use App\Models\Grading;
use App\Models\Result;
use App\Models\Students;
use Illuminate\Http\Request;
use Livewire\Component;

class Results extends Component
{

    public Students $student;

    public $gradings = [];

    public $totalCreditAttempted = 0;

    public $totalCreditEarned = 0;

    public $results = [];


    public function render()
    {
        return view('livewire.admin.transcript.results')->layout('livewire.admin.transcript.layout');
    }

    public function mount(Request $request, Students $student){
        $this->student = $student;

        $class = $student->_class();
        dd($student);
        $this->gradings = $class->program()->first()->gradingType->grading()->orderBy('weight','desc')->get() ?? [];
        $prog = $class->program;


        $this->results = $student->result->map(function ($result)  use ($prog){
            if($result->semester_id == "HND"){
                $this->totalCreditAttempted += 120;
                $this->totalCreditEarned += 120;
                $grade = Grading::find($result->exam_score);
                return collect([
                    'name' => "Higher National Diploma",
                    'year_name' => $result->year->name,
                    'semester_name' => "Entry Qualification",
                    'semester_id' => "HND",
                    'batch_id' => "HND",
                    'level' => "200",
                    'code' => "HND",
                    'type' => "C",
                    'cv' => "120",
                    'validated' => 1,
                    'ce' => 120,
                    'grade' => ($grade != "") ? $grade->grade : "-",
                    'gp' => isset($grade) ? $grade->weight : 0.0,
                ]);
            }else{
                foreach ($this->gradings as $key => $grade) {
                    $total = (($result->ca_score ?? 0 )+ ($result->exam_score ?? 0));
                    $passed = $total >= ($prog->ca_total + $prog->exam_total)*0.5;
                    if ($total >= $grade->lower && $total <= $grade->upper) {
                        if ($passed && !$result->validated) {
                            Result::where([
                                "student_id" => $result->student_id,
                                "subject_id" => $result->subject_id
                            ])->update(['validated' => 1]);
                        }
                        $result->refresh();

                        return collect([
                            'name' => $result->subject->name,
                            'year_name' => $result->year->name,
                            'semester_name' => $result->semester->name,
                            'semester_id' => $result->semester_id,
                            'batch_id' => $result->batch_id,
                            'level' => $this->student->_class()->level_id,
                            'code' => $result->subject->code,
                            'type' => $result->subject->status,
                            'cv' => $result->subject->coef,
                            'validated' => $result->validated,
                            'ce' => $passed ? $result->subject->coef : 0,
                            'grade' => ($grade != "") ? $grade->grade : "-",
                            'gp' => ($grade != "") ? $grade->weight : 0.0,
                        ]);


                    }
                }
            }

        })->sortBy('code');

        $this->totalCreditEarned =  $this->results->filter(function ($value, $key) {
            return $value['validated'] === 1;
        })->unique('code')->sum("cv");

        $this->totalCreditAttempted =  $this->results->unique('code')->sum("cv");


        $this->gpa = Helpers::getGPA($this->results);

        $this->results =    $this->results->groupBy('batch_id');

    }
}
