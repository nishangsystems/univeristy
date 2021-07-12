<?php

namespace Database\Seeders;

use App\Models\Batch;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Batch::create([
            'name' => '2021/2022',
        ]);
        Batch::create([
            'name' => '2022/2023',
        ]);
        Batch::create([
            'name' => '2023/2024',
        ]);
        Batch::create([
            'name' => '2024/2025',
        ]);
        Batch::create([
            'name' => '2025/2026',
        ]);
        Batch::create([
            'name' => '2026/2027',
        ]);
        Batch::create([
            'name' => '2027/2028',
        ]);
    }
}
