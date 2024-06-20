<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
                [
                    'remark' => 'Stock In'
                ],
                [
                    'remark' => 'Stock Out'
                ],
                [
                    'remark' => 'Wasted'
                ],
            ];

            \App\Models\Remarks::insertOrIgnore($data);
    }
}
