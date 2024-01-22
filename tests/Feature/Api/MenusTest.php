<?php

namespace Tests\Feature\Api;

use App\Models\Menu;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MenusTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected string $endpoint = 'api/menus/';

    /**
     * @var \App\Models\Menu
     */
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = Menu::factory()->create();
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
                        'price' => $this->model->getAttribute('price'),
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
                'name' => $this->model->getAttribute('name'),
            ]);
    }

    /** @test */
    public function create_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Menu::factory()->raw();

        $seenData = $data;

        $this->postJson($this->endpoint, $data)
            ->assertStatus(201)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function update_endpoint_works_as_expected()
    {
        // Submitted data
        $data = Menu::factory()->raw();

        $seenData = $data;

        $this->patchJson($this->endpoint.$this->model->getKey(), $data)
            ->assertStatus(200)
            ->assertJsonFragment($seenData);
    }

    /** @test */
    public function delete_endpoint_works_as_expected()
    {
        $this->assertDatabaseHas('menus', [
            'name' => $this->model->getAttribute('name'),
            'price' => $this->model->getAttribute('price'),
        ]);

        $this->deleteJson($this->endpoint.$this->model->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'success',
            ]);

        $this->assertDatabaseMissing('menus', [
            'name' => $this->model->getAttribute('name'),
            'price' => $this->model->getAttribute('price'),
            'deleted_at' => null
        ]);
    }
}
