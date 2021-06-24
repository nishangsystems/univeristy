<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {

        DB::table('students')->insert([
            'name' =>  'ADANO NCHA ANGEL BRIGHT',
            'email' => 'ADANO',
            'password' => Hash::make('password'),
            'gender'  => $faker->randomElement(['male', 'female']),
            'matric' => 'F1G20SBB002',
            'phone' => $faker->phoneNumber,
            'dob' => $faker->date(),
            'pob' => $faker->address,
            'address' => $faker->address,
            'admission_batch_id' => 1
        ]);
        DB::table('students')->insert([
            'name' =>  'AJOUH ARAA NAMONDO',
            'email' => 'AJOUH',
            'password' => Hash::make('password'),
            'gender'  => $faker->randomElement(['male', 'female']),
            'matric' => ' F1G20SBB003',
            'phone' => $faker->phoneNumber,
            'dob' => $faker->date(),
            'pob' => $faker->address,
            'address' => $faker->address,
            'admission_batch_id' => 2
        ]);
        DB::table('students')->insert([
            'name' =>  'NCHA ANGEL BRIGHT',
            'email' => 'NCHA',
            'password' => Hash::make('password'),
            'gender'  => $faker->randomElement(['male', 'female']),
            'matric' => 'F1G20SBB004',
            'phone' => $faker->phoneNumber,
            'dob' => $faker->date(),
            'pob' => $faker->address,
            'address' => $faker->address,
            'admission_batch_id' => 3
        ]);
    }
}
