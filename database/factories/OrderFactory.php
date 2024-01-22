<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $total = $this->faker->numberBetween(100000, 200000);

        return [
            'table_id' => Table::factory(),
            'total' => $total,
            'payment' => $total
        ];
    }
}
