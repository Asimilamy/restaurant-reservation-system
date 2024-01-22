<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 5; $i++) {
            Order::factory(1)->create([
                'table_id' => Table::inRandomOrder()->first() ?? Table::factory()
            ])->map(function (Order $order) {

                for ($j = 0; $j < rand(1, 5); $j++) {
                    $menu = Menu::inRandomOrder()->first() ?? Menu::factory();
                    $qty = rand(1, 5);
                    $total = $menu->price * $qty;

                    OrderDetail::factory(1)->create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'price' => $menu->price,
                        'qty' => $qty,
                        'total' => $total
                    ]);
                }

                $details = $order->details;
                $total = $details->sum('total') ?? 0;
                $order->fill([
                    'total' => $total,
                    'payment' => $total
                ]);
                $order->save();
            });
        }
    }
}
