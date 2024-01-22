<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationsTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected string $endpoint = 'api/reservations/';

    /**
     * @var \App\Models\Reservation
     */
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = Reservation::factory()->create();
    }

    /** @test */
    public function index_endpoint_works_as_expected()
    {
        $this->getJson($this->endpoint)
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => $this->model->getAttribute('name')
            ]);
    }

    /** @test */
    public function show_endpoint_works_as_expected()
    {
        $this->getJson($this->endpoint.$this->model->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => $this->model->getAttribute('name'),
            ]);
    }

    /** @test */
    public function create_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Reservation::factory()->raw();

        $seenData['table_id'] = $data['table_id'];

        $this->postJson($this->endpoint, $data)
            ->assertStatus(201)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function update_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Reservation::factory()->raw();

        $seenData['table_id'] = $data['table_id'];

        $this->patchJson($this->endpoint.$this->model->getKey(), $data)
            ->assertStatus(200)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function delete_endpoint_works_as_expected()
    {
        $this->assertDatabaseHas('reservations', [
            'table_id' => $this->model->getAttribute('table_id'),
            'name' => $this->model->getAttribute('name'),
        ]);

        $this->deleteJson($this->endpoint.$this->model->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'success',
            ]);

        $this->assertDatabaseMissing('reservations', [
            'table_id' => $this->model->getAttribute('table_id'),
            'name' => $this->model->getAttribute('name'),
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function payment_endpoint_works_as_expected()
    {
        $data = Reservation::factory()->raw();
        for ($i = 0; $i < rand(1, 5); $i++) {
            $data['order_details'][] = OrderDetail::factory()->raw();
        }

        $reservation = $this->postJson($this->endpoint, $data)
            ->assertStatus(201)
            ->decodeResponseJson()['data'];
        $paymentData = [
            'order_id' => $reservation['order_id'],
            'payment' => $reservation['order']['total']
        ];
        $this->postJson('api/orders/payment', $paymentData)
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'success'
            ]);
    }
}
