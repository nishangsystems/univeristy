<?php

namespace App\Http\Livewire\Admin;

use App\Models\Students;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UnvalidatedCourses extends Component
{

    public $courses = [];
    public $title = '';
    public $student = null;

    public function render()
    {
        return view('livewire.admin.unvalidated-courses', ['courses'=>$this->courses])->layout('admin.layout');
    }

    public function mount(Students $student){
        $this->student = $student;
        $this->title = "Unvalidated Courses For {$student->name} [{$student->matric}]";
        $this->courses = $student->result()->join('subjects', ['subjects.id'=>'results.subject_id'])
            ->select(['subjects.code', 'subjects.name', DB::raw("SUM(results.ca_score + results.exam_score) as total_mark"), 'results.ca_score', 'results.exam_score', 'results.semester_id'])
            ->groupBy('results.id')->having('total_mark', '>', 50)->get();
        
    }
}
