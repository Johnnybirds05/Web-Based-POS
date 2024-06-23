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
                    'remark' => 'Opening'
                ],
                [
                    'remark' => 'Sell'
                ],
                [
                    'remark' => 'Delivery'
                ],
                [
                    'remark' => 'Damage'
                ],
                [
                    'remark' => 'Closing'
                ],
            ];

            \App\Models\Remarks::insertOrIgnore($data);
    }
}
