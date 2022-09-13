<?php

namespace Database\Seeders;

use App\Models\Students;
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

        Students::create([
            'name' =>  'ADANO NCHA ANGEL BRIGHT',
            'email' => 'ADANO',
            'password' => Hash::make('password'),
            'gender'  => $faker->randomElement(['male', 'female']),
            'matric' => 'F1G20SBB002',
            'phone' => $faker->phoneNumber,
            'dob' => $faker->date(),
            'pob' => $faker->address,
            'address' => $faker->address,
            'admission_batch_id' => 1,
            'type' => 'boarding'
        ]);
        Students::create([
            'name' =>  'AJOUH ARAA NAMONDO',
            'email' => 'AJOUH',
            'password' => Hash::make('password'),
            'gender'  => $faker->randomElement(['male', 'female']),
            'matric' => ' F1G20SBB003',
            'phone' => $faker->phoneNumber,
            'dob' => $faker->date(),
            'pob' => $faker->address,
            'address' => $faker->address,
            'admission_batch_id' => 2,
            'type' => 'day'
        ]);
        Students::create([
            'name' =>  'NCHA ANGEL BRIGHT',
            'email' => 'NCHA',
            'password' => Hash::make('password'),
            'gender'  => $faker->randomElement(['male', 'female']),
            'matric' => 'F1G20SBB004',
            'phone' => $faker->phoneNumber,
            'dob' => $faker->date(),
            'pob' => $faker->address,
            'address' => $faker->address,
            'admission_batch_id' => 3,
            'type' => 'boarding'
        ]);
        Students::create([
            'name' =>  'ATEMKENG  RANDY JOE',
            'email' => 'ATEMKENG',
            'password' => Hash::make('password'),
            'gender'  => $faker->randomElement(['male', 'female']),
            'matric' => 'F1G20SBB005',
            'phone' => $faker->phoneNumber,
            'dob' => $faker->date(),
            'pob' => $faker->address,
            'address' => $faker->address,
            'admission_batch_id' => 4,
            'type' => 'boarding',
            'created_at' => '2020-12-01 00:00:00',
            'updated_at' => '2020-12-01 00:00:00'
        ]);
    }
}
