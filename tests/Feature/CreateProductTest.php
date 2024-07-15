<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function getFakeImage()
    {
        return UploadedFile::fake()->image('product.jpg');
    }

    public function test_create_product_api()
    {
        $fakeImage = $this->getFakeImage();

        $payload = [
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 10.99,
            'image' => $fakeImage,
            'categories' => [1, 2, 3]
        ];

        $response = $this->post('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
        ]);
    }

    public function test_create_product_cli()
    {
        $image = $this->getFakeImage();

        $this->artisan('product:create')
            ->expectsQuestion('name', 'Test Product')
            ->expectsQuestion('description', 'Test description')
            ->expectsQuestion('price', 10.99)
            ->expectsQuestion('image', $image->getRealPath())
            ->expectsQuestion('Enter category IDs (comma-separated)', '1,2,3')
            ->assertExitCode(1);
    }

    public function test_product_creation_validation()
    {
        // Test with missing required fields
        $response = $this->postJson('/api/products', []);
        $response->assertStatus(422);

        // Test with invalid data types or formats
        $response = $this->postJson('/api/products', [
            'name' => '',
            'description' => 'Test description',
            'price' => 'invalid_price',
            'image' => UploadedFile::fake()->create('test.txt', 100),
            'categories' => '1,2,3'
        ]);
        $response->assertStatus(422);

        // Test with exceeding max length
        $response = $this->postJson('/api/products', [
            'name' => str_repeat('a', 256), // Exceeds max length
            'description' => 'Test description',
            'price' => 10.99,
            'image' => $this->getFakeImage(),
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $validImage = $this->getFakeImage();

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 10.99,
            'image' => $validImage,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
        ]);
    }
}
