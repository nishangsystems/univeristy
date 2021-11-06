<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder{

      public function run()
      {

            $faker = Faker\Factory::create();
            $date = new \DateTime();
            foreach(config('constants.PERMISSION_GROUPS') as $group){
                DB::table('permissions')->insert([
                    'name' => "Manage ".$group['name'],
                    'slug' =>  strtolower("manage_".$group['name']),
                ]);
            }

      }
}
