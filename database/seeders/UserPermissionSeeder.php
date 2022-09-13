<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$faker = Faker\Factory::create();
        $date = new \DateTime();
        foreach(\App\Models\Permission::all() as $permission){
            \DB::table('users_permissions')->insert([
               'user_id' => 1,
               'permission_id'=>$permission->id,
            ]);
        }
    }
}
