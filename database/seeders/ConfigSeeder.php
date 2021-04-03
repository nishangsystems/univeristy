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

        \App\Models\Config::create([
            'year_id' => 1,
        ]);

    }
}
