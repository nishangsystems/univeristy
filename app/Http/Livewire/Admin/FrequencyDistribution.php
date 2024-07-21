<?php

namespace App\Http\Livewire\Admin;

use App\Helpers\Helpers;
use App\Http\Livewire\DataTable\DataTable;
use App\Models\ClassSubject;
use App\Models\FAQ;
use App\Models\Level;
use App\Models\ProgramLevel;
use App\Models\Result;
use App\Models\School;
use App\Models\SchoolUnits;
use Livewire\Component;

class FrequencyDistribution extends Component
{
    use DataTable;

    public $title = "";
    public $_title = "";
    public $class_id = "";
    public $semester_id = "";
    public $year_id = "";

    public $programs = [];
    public $levels = [];

    public $students = [];
    public $grades = [];
    private $subjects = [];
    public $course_masters = [];

    public $base_pass;



    public function mount()
    {
        $this->title = __('text.frequency_distribution');
        $this->courses = collect([]);
        $this->grades = collect([]);

        $this->resetFilters();
        $this->perPage = 10000;
        $this->page = 1;
        $this->programs =  SchoolUnits::where(['unit_id'=> 4])->whereHas('classes', function ($q){
            $q->whereHas('class_subjects');
        })->get()->pluck('name', 'id');

    }


    public function render()
    {

        return view('livewire.admin.frequency-distribution', ['subjects'=> $this->subjects])->layout('admin.layout');
    }

    protected function getBaseQuery()
    {
        $filters = $this->filters;
        return ClassSubject::query()->select('class_subjects.*')
            ->whereHas('subject');
    }

    public function resetFilters(){
        $this->filters = [
            'program_id'=>'',
            'level_id'=>'',
            'semester_id'=>Helpers::getCurrentSemester(),
            'year_id'=>Helpers::getCurrentAccademicYear(),
        ];

    }

    public function filterProgramId($query, $value)
    {
        if (strlen($value) === 0) {
            return $query;
        }

        return $query->whereHas('class', function ($q) use ($value){
            return $q->where('program_id', $value);
        });
    }

    public function filterLevelId($query, $value)
    {
        if (strlen($value) === 0) {
            return $query;
        }

        return $query->whereHas('class', function ($q) use ($value){
            return $q->where('level_id', $value);
        });
    }

    public function filterSemesterId($query, $value)
    {
        if (strlen($value) === 0) {
            return $query;
        }

        return $query->whereHas('subject', function ($q) use ($value){
            return $q->whereHas('results', function ($q1) use ($value){
                return $q1->where('semester_id', $value);
            });
        });
    }

    public function filterYearId($query, $value)
    {
        if (strlen($value) === 0) {
            return $query;
        }

        return $query->whereHas('subject', function ($q) use ($value){
            return $q->whereHas('results', function ($q1) use ($value){
                return $q1->where('batch_id', $value);
            });
        });
    }

    public function updated($key , $value){

        if($key == "filters.program_id"){
            $this->levels = ProgramLevel::where(['program_id'=>$value])->get()->pluck('level_id', 'level_id');
            $this->grades = SchoolUnits::find($value)?->gradingType?->grading?->sortBy('grade') ?? [];
            $this->base_pass = (SchoolUnits::find($value)->ca_total ?? 0 + SchoolUnits::find($value)->exam_total ?? 0)*0.5;
        }

        if($key == "filters.year_id" && !empty($this->filters['program_id'])){

        }
        $this->subjects = collect([]);
    }

    public function getData(){
//        $this->_title = $class->name().' '.$semester->name.' '.__('text.frequency_distribution').' FOR '.$year->name.' '.__('text.academic_year');
        $this->_title = __('text.frequency_distribution');
        $this->subjects = $this->rows;

   }
}
