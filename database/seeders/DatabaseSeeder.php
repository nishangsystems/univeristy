<?php

use Database\Seeders\BatchSeeder;
use Database\Seeders\ClassSubjectSeeder;
use Database\Seeders\SchoolUnitSeeder;
use Database\Seeders\StudentClassSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(ConfigSeeder::class);
        $this->call(SchoolUnitSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(StudentClassSeeder::class);
        $this->call(ClassSubjectSeeder::class);
        $this->call(BatchSeeder::class);
    }
}
