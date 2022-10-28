<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Config;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Ni shang',
            'email' => 'nishang80@gmail.com',
            'username' => 'nishang80@gmail.com',
            'password' => Hash::make('password'),
            'type'     => 'teacher',
            'gender' => 'male',
            'address' => 'New Town',
            'phone' => '7485892354980'
        ]);

        User::create([
            'name' => 'Peter Bery J',
            'username' => 'PeterBeryJ',
            'email' => 'peterberyj@gmail.com',
            'password' =>  Hash::make('password'),
            'type'     => 'teacher',
            'gender' => 'male',
            'address' => 'New Town',
            'phone' => '7489354980'
        ]);

        Config::create([
            'seq_id' => 5,
            'year_id' => 1,
            'start_date' => '2021-09-09',
            'end_date' => '2021-12-02'
        ]);
    }
}
