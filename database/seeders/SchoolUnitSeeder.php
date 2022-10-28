<?php

namespace Database\Seeders;

use App\Models\SchoolUnits;
use Illuminate\Database\Seeder;

class SchoolUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SchoolUnits::create([
            'name' => 'GRAMMAR',
            'unit_id' => 1,
            'parent_id' => 0,
            'prefix' => 'GM',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'COMMCERCIAL',
            'unit_id' => 1,
            'parent_id' => 0,
            'prefix' => 'cM',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'TECHNICAL',
            'unit_id' => 1,
            'parent_id' => 0,
            'prefix' => 'TN',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'FIRST CYCLE',
            'unit_id' => 2,
            'parent_id' => 1,
            'prefix' => 'FC',
            'suffix' => NULL
        ]);
        SchoolUnits::create([
            'name' => 'SECOND CYCLE',
            'unit_id' => 2,
            'parent_id' => 1,
            'prefix' => 'SC',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form One',
            'unit_id' => 3,
            'parent_id' => 4,
            'prefix' => 'F1G',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Two',
            'unit_id' => 3,
            'parent_id' => 4,
            'prefix' => 'F2G',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Three',
            'unit_id' => 3,
            'parent_id' => 4,
            'prefix' => 'F3G',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Four',
            'unit_id' => 3,
            'parent_id' => 4,
            'prefix' => 'F4G',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Five',
            'unit_id' => 3,
            'parent_id' => 4,
            'prefix' => 'F5G',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'FIRST CYCLE',
            'unit_id' => 2,
            'parent_id' => 2,
            'prefix' => 'FC',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'SECOND CYCLE',
            'unit_id' => 2,
            'parent_id' => 2,
            'prefix' => 'SC',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form One',
            'unit_id' => 3,
            'parent_id' => 16,
            'prefix' => 'F1C',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Two',
            'unit_id' => 3,
            'parent_id' => 16,
            'prefix' => 'F2C',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Three',
            'unit_id' => 3,
            'parent_id' => 16,
            'prefix' => 'F3C',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Four',
            'unit_id' => 3,
            'parent_id' => 16,
            'prefix' => 'F4C',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Form Five',
            'unit_id' => 3,
            'parent_id' => 16,
            'prefix' => 'F5C',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Marketing',
            'unit_id' => 3,
            'parent_id' => 17,
            'prefix' => 'MK',
            'suffix' => 'SBB'
        ]);
        SchoolUnits::create([
            'name' => 'Accounting',
            'unit_id' => 3,
            'parent_id' => 17,
            'prefix' => 'AC',
            'suffix' => 'SBB'
        ]);
        

    }
}
