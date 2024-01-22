<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'name' => 'French Fries',
                'price' => 15000
            ],
            [
                'name' => 'Fried Chicken',
                'price' => 20000
            ],
            [
                'name' => 'Rice',
                'price' => 5000
            ],
            [
                'name' => 'Cola',
                'price' => 7500
            ],
            [
                'name' => 'Mineral Water',
                'price' => 5000
            ],
        ];

        foreach ($menus as $menu) {
            $model = new Menu();
            $model->fill([
                'name' => $menu['name'],
                'price' => $menu['price']
            ]);
            $model->save();
        }
    }
}
