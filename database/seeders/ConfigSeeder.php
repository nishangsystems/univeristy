<?php


use App\Models\User;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (\App\Models\Config::all()->count() > 0) {
            return;
        }

        \App\Models\Batch::create([
            'name' => "2020/2021",
        ]);


        if (\App\Models\Sequence::all()->count() > 0) {
            return;
        }

        for($i = 1; $i<=6; $i++){
            \App\Models\Sequence::create([
                'name' => "2020/2021",
            ]);
        }

    }
}
