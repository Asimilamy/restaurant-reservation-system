<?php

namespace Tests\Feature\Api;

use App\Models\Table;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TablesTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected string $endpoint = 'api/tables/';

    /**
     * @var \App\Models\Table
     */
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = Table::factory()->create();
    }

    /** @test */
    public function index_endpoint_works_as_expected()
    {
        $this->getJson($this->endpoint)
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    [
                        'id' => $this->model->getAttribute('id'),
                        'name' => $this->model->getAttribute('name'),
                        'created_at' => $this->model->getAttribute('created_at'),
                        'updated_at' => $this->model->getAttribute('updated_at'),
                    ]
                ]
            ]);
    }

    /** @test */
    public function show_endpoint_works_as_expected()
    {
        $this->getJson($this->endpoint.$this->model->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->model->getAttribute('id'),
                'name' => $this->model->getAttribute('name'),
                'created_at' => $this->model->getAttribute('created_at'),
                'updated_at' => $this->model->getAttribute('updated_at'),
            ]);
    }

    /** @test */
    public function create_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Table::factory()->raw();

        // The data which should be shown
        $seenData['name'] = $data['name'];

        $this->postJson($this->endpoint, $data)
            ->assertStatus(201)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function update_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Table::factory()->raw();

        // The data which should be shown
        $seenData['name'] = $data['name'];

        $this->patchJson($this->endpoint.$this->model->getKey(), $data)
            ->assertStatus(200)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function delete_endpoint_works_as_expected()
    {
        $this->assertDatabaseHas('tables', [
            'name' => $this->model->getAttribute('name'),
        ]);

        $this->deleteJson($this->endpoint.$this->model->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'success',
            ]);

        $this->assertDatabaseMissing('tables', [
            'name' => $this->model->getAttribute('name'),
        ]);
    }
}
