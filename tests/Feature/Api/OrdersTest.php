<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected string $endpoint = 'api/orders/';

    /**
     * @var \App\Models\Order
     */
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = Order::factory()->create();
    }

    /** @test */
    public function index_endpoint_works_as_expected()
    {
        $this->getJson($this->endpoint)
            ->assertStatus(200)
            ->assertJsonFragment([
                'table_id' => $this->model->getAttribute('table_id'),
                'total' => $this->model->getAttribute('total')
            ]);
    }

    /** @test */
    public function show_endpoint_works_as_expected()
    {
        $this->getJson($this->endpoint.$this->model->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'table_id' => $this->model->getAttribute('table_id'),
                'total' => $this->model->getAttribute('total'),
            ]);
    }

    /** @test */
    public function create_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Order::factory()->raw();

        $seenData['table_id'] = $data['table_id'];

        $this->postJson($this->endpoint, $data)
            ->assertStatus(201)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function update_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Order::factory()->raw();

        $seenData['table_id'] = $data['table_id'];

        $this->patchJson($this->endpoint.$this->model->getKey(), $data)
            ->assertStatus(200)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function delete_endpoint_works_as_expected()
    {
        $this->assertDatabaseHas('orders', [
            'table_id' => $this->model->getAttribute('table_id'),
            'total' => $this->model->getAttribute('total'),
        ]);

        $this->deleteJson($this->endpoint.$this->model->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'success',
            ]);

        $this->assertDatabaseMissing('orders', [
            'table_id' => $this->model->getAttribute('table_id'),
            'total' => $this->model->getAttribute('total'),
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function payment_endpoint_works_as_expected()
    {
        $data = Order::factory()->raw();
        for ($i = 0; $i < rand(1, 5); $i++) {
            $data['details'][] = OrderDetail::factory()->raw();
        }

        $order = $this->postJson($this->endpoint, $data)
            ->assertStatus(201)
            ->decodeResponseJson()['data'];
        $paymentData = [
            'order_id' => $order['id'],
            'payment' => $order['total']
        ];
        $this->postJson($this->endpoint . 'payment', $paymentData)
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'success'
            ]);
    }
}
