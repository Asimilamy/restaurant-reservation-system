<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startAt = Carbon::parse()
            ->subDays(rand(1, 10))
            ->subMonths(rand(1, 6))
            ->format('Y-m-d H:i:s');

        return [
            'table_id' => Table::factory(),
            'name' => $this->faker->name(),
            'start_at' => $startAt
        ];
    }
}
