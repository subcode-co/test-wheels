<?php

namespace Database\Seeders;

use App\Models\Prize;
use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    public function run(): void
    {
        $prizes = [
            ['name' => 'استشارة مجانية', 'code' => 'free_consultation', 'probability_weight' => 3, 'display_order' => 1, 'color' => '#0d9488', 'is_winner' => true],
            ['name' => 'فحص مجاني', 'code' => 'free_checkup', 'probability_weight' => 3, 'display_order' => 2, 'color' => '#2563eb', 'is_winner' => true],
            ['name' => 'إجراء مجاني', 'code' => 'free_procedure', 'probability_weight' => 1, 'display_order' => 3, 'color' => '#7c3aed', 'is_winner' => true],
            ['name' => 'خصم 50%', 'code' => 'discount_50', 'probability_weight' => 2, 'display_order' => 4, 'color' => '#c026d3', 'is_winner' => true],
            ['name' => 'عروض حصرية', 'code' => 'exclusive_offers', 'probability_weight' => 2, 'display_order' => 5, 'color' => '#dc2626', 'is_winner' => true],
            ['name' => 'حظ أوفر', 'code' => 'no_win', 'probability_weight' => 2, 'display_order' => 6, 'color' => '#95a5a6', 'is_winner' => false],
        ];

        foreach ($prizes as $data) {
            Prize::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
