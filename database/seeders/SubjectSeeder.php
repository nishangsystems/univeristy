<?php


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
        if (\App\Models\Subjects::all()->count() > 0) {
            return;
        }

//        $levels = ['English','French','Mathematics','History','Geography','English Literature','Chemistry','Physics','Biology'];
//        foreach ($levels as $level) {
//            \App\Models\Subjects::create([
//                'name' => $level,
//            ]);
//        }
    }
}
