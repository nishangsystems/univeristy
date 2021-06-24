<?php

namespace Database\Seeders;

use App\Models\StudentClass;
use Illuminate\Database\Seeder;

class StudentClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StudentClass::create([
            'student_id' => 1,
            'class_id' => 6,
            'year_id' => 1
        ]);
        StudentClass::create([
            'student_id' => 2,
            'class_id' => 6,
            'year_id' => 1
        ]);
        StudentClass::create([
            'student_id' => 3,
            'class_id' => 6,
            'year_id' => 1
        ]);
    }
}
