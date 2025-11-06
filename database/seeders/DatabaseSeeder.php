<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Seed minimal data for quick demos
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tables = collect([
            ['number' => 'A1'],
            ['number' => 'A2'],
            ['number' => 'VIP'],
        ])->map(function ($table) {
            return Table::updateOrCreate(
                ['number' => $table['number']],
                [
                    'code' => Str::upper(Str::random(6)),
                    'is_active' => true,
                ]
            );
        });

        $menus = [
            [
                'name' => 'Nasi Goreng Kahuripan',
                'category' => 'Makanan',
                'price' => 28000,
                'stock' => 50,
                'description' => 'Nasi goreng spesial dengan topping ayam dan telur.',
                'options' => [
                    ['name' => 'Level Pedas', 'type' => 'spice', 'extra_price' => 0],
                    ['name' => 'Tambah Sate Ayam', 'type' => 'addon', 'extra_price' => 8000],
                ],
            ],
            [
                'name' => 'Mie Kuah Sunda',
                'category' => 'Makanan',
                'price' => 24000,
                'stock' => 45,
                'description' => 'Mie kuah gurih dengan sayuran segar.',
                'options' => [
                    ['name' => 'Telur Rebus', 'type' => 'addon', 'extra_price' => 5000],
                ],
            ],
            [
                'name' => 'Sate Maranggi',
                'category' => 'Makanan',
                'price' => 32000,
                'stock' => 40,
                'description' => 'Sate khas Purwakarta dengan sambal kecap.',
                'options' => [],
            ],
            [
                'name' => 'Es Teh Kahuripan',
                'category' => 'Minuman',
                'price' => 8000,
                'stock' => 100,
                'description' => 'Es teh manis dengan aroma melati.',
                'options' => [
                    ['name' => 'Less Sugar', 'type' => 'preference', 'extra_price' => 0],
                ],
            ],
            [
                'name' => 'Kopi Tubruk Nusantara',
                'category' => 'Minuman',
                'price' => 15000,
                'stock' => 60,
                'description' => 'Kopi hitam pekat dengan gula aren.',
                'options' => [
                    ['name' => 'Tambah Susu', 'type' => 'addon', 'extra_price' => 3000],
                ],
            ],
        ];

        foreach ($menus as $menuData) {
            $menu = Menu::updateOrCreate(
                ['name' => $menuData['name']],
                [
                    'category' => $menuData['category'],
                    'description' => $menuData['description'],
                    'price' => $menuData['price'],
                    'stock' => $menuData['stock'],
                    'is_active' => true,
                ]
            );

            $menu->options()->delete();
            foreach ($menuData['options'] as $option) {
                $menu->options()->create($option);
            }
        }

        User::updateOrCreate(
            ['email' => 'admin@restokahuripan.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cashier@restokahuripan.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kitchen@restokahuripan.com'],
            [
                'name' => 'Koki',
                'password' => Hash::make('password'),
                'role' => 'kitchen',
            ]
        );
    }
}
