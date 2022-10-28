<?php


use App\Models\User;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (\App\Models\Unit::all()->count() > 0) {
            return;
        }

        $levels = ['BACKGROUND','SECTION','SERIES','CLASS'];
        foreach ($levels as $level) {
            \App\Models\Unit::create([
                'name' => $level,
            ]);
        }
    }
}
