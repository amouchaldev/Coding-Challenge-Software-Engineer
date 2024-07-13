<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CategorySeeder::class
        ]);
    }

    public function test_create_product_api()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('product.jpg');

        $payload = [
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 10.99,
            'image' => $image,
            'categories' => [1, 2, 3]
        ];

        $response = $this->post('/api/products', $payload);
        // dd($response->getContent());

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
        ]);

        Storage::disk('public')->assertExists('products/' . $image->hashName());
    }

    public function test_product_creation_validation()
    {
        // Test with missing required fields
        $response = $this->postJson('/api/products', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'price', 'image']);

        // Test with invalid data types or formats
        $response = $this->postJson('/api/products', [
            'name' => '',
            'description' => 'Test description',
            'price' => 'invalid_price',
            'image' => UploadedFile::fake()->create('test.txt', 100),
            'categories' => '1,2,3'
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'image']);

        // Test with exceeding max length
        $response = $this->postJson('/api/products', [
            'name' => str_repeat('a', 256), // Exceeds max length
            'description' => 'Test description',
            'price' => 10.99,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Test with valid data
        Storage::fake('public');
        $validImage = UploadedFile::fake()->image('product.jpg');

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

        Storage::disk('public')->assertExists('products/' . $validImage->hashName());
    }
}
