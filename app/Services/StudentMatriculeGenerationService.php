<?php
namespace App\Services;

use App\Models\Batch;
use App\Models\SchoolUnits;
use App\Models\Students;

class StudentMatriculeGenerationService {

    public function generateMatricule($class, $year)
    {
        $lastStudentWithMatricule = $this->getLastStudentWithMatriculeBySchoolUnitAndYear($class, $year);
        $students = $this->getStudentsBySchoolUnitsAndYear($class, $year);
        $class = $this->getStudentClass($class);
        $batch = $this->getStudentBatch($year);

        if(count($lastStudentWithMatricule->toArray()) != 0){
            $lastMatric = (int)substr($lastStudentWithMatricule[0]->matric, -3);
            foreach($students as $student){
                $student->matric = $class->prefix . substr($batch->name, 2, 2) . $class->suffix . str_pad(($lastMatric + 1), 3, 0, STR_PAD_LEFT);
                $student->save();
            }
        }else {
            foreach ($students as $k => $student) {
                $student->matric = $class->prefix . substr($batch->name, 2, 2) . $class->suffix . str_pad(($k + 1), 3, 0, STR_PAD_LEFT);
                $student->save();
            }
        }

    }

    private function getStudentsBySchoolUnitsAndYear($class_id, $year_id)
    {
        $base_units = $this->findStudents($class_id, $year_id)
                    ->whereNull('students.matric')
                    ->get();
        return $base_units;
    }


    private  function getLastStudentWithMatriculeBySchoolUnitAndYear($class_id, $year_id)
    {
        $student = $this->findStudents($class_id, $year_id)
                    ->whereNotNull('students.matric')
                    ->latest()->get();

        return $student;
    }

    private function findStudents($class_id, $year_id)
    {
        return  Students::select('students.*')
                    ->join('student_classes', ['students.id' => 'student_classes.student_id'])
                    ->join('school_units', ['student_classes.class_id' => 'school_units.id'])
                    ->join('batches', ['student_classes.year_id' => 'batches.id'])
                    ->where('student_classes.class_id', $class_id)
                    ->where('student_classes.year_id', $year_id);

    }

    private function getStudentClass($class_id)
    {
        $class = SchoolUnits::find($class_id);
        return $class;
    }

    private function getStudentBatch($year_id)
    {
        $batch = Batch::find($year_id);

        return $batch;
    }
}
