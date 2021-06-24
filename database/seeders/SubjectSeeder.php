<?php

use App\Models\Subjects;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subjects::create([
            'name' => 'Geograph',
            'coef' => 3
        ]);

        Subjects::create([
            'name' => 'English',
            'coef' => 3
        ]);

        Subjects::create([
            'name' => 'French',
            'coef' => 3
        ]);
        Subjects::create([
            'name' => 'Maths',
            'coef' => 5
        ]);
        Subjects::create([
            'name' => 'Legislation',
            'coef' => 3
        ]);
        Subjects::create([
            'name' => 'Biology',
            'coef' => 3
        ]);
    }
}
