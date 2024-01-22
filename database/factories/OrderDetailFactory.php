<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $menuName = function (array $attributes) {
            return Menu::find($attributes['menu_id'])->name;
        };
        /**
         * @var int
         */
        $menuPrice = function (array $attributes) {
            return Menu::find($attributes['menu_id'])->price;
        };
        $qty = $this->faker->numberBetween(1, 5);
        $total = function (array $attributes) use ($qty) {
            return Menu::find($attributes['menu_id'])->price * $qty;
        };

        return [
            'order_id' => Order::factory(),
            'menu_id' => Menu::factory(),
            'menu_name' => $menuName,
            'price' => $menuPrice,
            'qty' => $qty,
            'total' => $total
        ];
    }
}
