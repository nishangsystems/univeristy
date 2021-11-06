<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesPermissionSeeder extends Seeder
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
            \DB::table('roles_permissions')->insert([
               'role_id' => 1,
               'permission_id'=>$permission->id,
            ]);
        }
    }
}
