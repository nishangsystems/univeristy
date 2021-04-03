<?php


use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::where('type', 'admin')->count() > 0) {
            return;
        }

        $user = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'type' => 'admin',
        ]);
    }
}
