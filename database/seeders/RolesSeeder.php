<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // $faker = Faker\Factory::create();
        $date = new \DateTime();
        foreach(config('constants.ROLES') as $role){
            \DB::table('roles')->insert([
            'name' => $role,
            'slug' => strtolower($role),
            ]);
        }
    }
}
