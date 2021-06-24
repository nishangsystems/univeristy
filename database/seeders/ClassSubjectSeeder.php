<?php

namespace Database\Seeders;

use App\Models\ClassSubject;
use Illuminate\Database\Seeder;

class ClassSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClassSubject::create([
            'class_id' => 6,
            'subject_id' => 1
        ]);
        ClassSubject::create([
            'class_id' => 6,
            'subject_id' => 2
        ]);
        ClassSubject::create([
            'class_id' => 6,
            'subject_id' => 3
        ]);
        ClassSubject::create([
            'class_id' => 6,
            'subject_id' => 4
        ]);
        ClassSubject::create([
            'class_id' => 6,
            'subject_id' => 5
        ]);
        ClassSubject::create([
            'class_id' => 6,
            'subject_id' => 6
        ]);
    }
}
