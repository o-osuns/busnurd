<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('public');
    }

    #[Test]
    public function it_handles_duplicate_slug_validation_error()
    {
        Product::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => 'New Product',
                'slug' => 'existing-slug',
                'price' => 99.99,
            ]);

        $response->assertSessionHasErrors(['slug']);
        $this->assertEquals(1, Product::count()); // Only the original product
    }

    /*
    #[Test]
    public function it_handles_invalid_image_upload()
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => 'Product with Invalid Image',
                'slug' => 'product-with-invalid-image',
                'price' => 99.99,
                'image' => $invalidFile,
            ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertEquals(0, Product::count());
    }

    #[Test]
    public function it_handles_oversized_image_upload()
    {
        $largeImage = UploadedFile::fake()->image('large.jpg')->size(3000); // 3MB

        $response = $this->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => 'Product with Large Image',
                'slug' => 'product-with-large-image',
                'price' => 99.99,
                'image' => $largeImage,
            ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertEquals(0, Product::count());
    }
    */

    #[Test]
    public function it_handles_missing_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('products.store'), []);

        $response->assertSessionHasErrors(['name', 'slug', 'price']);
        $this->assertEquals(0, Product::count());
    }

    #[Test]
    public function it_handles_invalid_price_formats()
    {
        $invalidPrices = [
            'not-a-number',
            'abc',
            '',
            -10.50,
            '10.999', // Too many decimal places
        ];

        foreach ($invalidPrices as $invalidPrice) {
            $response = $this->actingAs($this->user)
                ->post(route('products.store'), [
                    'name' => 'Test Product',
                    'slug' => 'test-product-' . uniqid(),
                    'price' => $invalidPrice,
                ]);

            $response->assertSessionHasErrors(['price']);
        }

        $this->assertEquals(0, Product::count());
    }

    #[Test]
    public function it_handles_extremely_long_names()
    {
        $longName = str_repeat('A', 300);

        $response = $this->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => $longName,
                'slug' => 'long-name-product',
                'price' => 99.99,
            ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertEquals(0, Product::count());
    }

    #[Test]
    public function it_handles_concurrent_slug_creation()
    {
        // This test simulates two products being created simultaneously with the same name
        // In a real scenario, this might cause race conditions

        $productData = [
            'name' => 'Concurrent Product',
            'slug' => 'concurrent-product',
            'price' => 99.99,
        ];

        // Create first product
        $response1 = $this->actingAs($this->user)
            ->post(route('products.store'), $productData);

        // Attempt to create second product with same slug
        $response2 = $this->actingAs($this->user)
            ->post(route('products.store'), $productData);

        // Second request should fail due to slug uniqueness
        $response2->assertSessionHasErrors(['slug']);
        $this->assertEquals(1, Product::count());
    }

    #[Test]
    public function it_handles_update_conflicts()
    {
        $product1 = Product::factory()->create(['slug' => 'product-one']);
        $product2 = Product::factory()->create(['slug' => 'product-two']);

        // Try to update product2 to have the same slug as product1
        $response = $this->actingAs($this->user)
            ->put(route('products.update', $product2), [
                'name' => $product2->name,
                'slug' => 'product-one', // Conflicts with product1
                'price' => $product2->price,
            ]);

        $response->assertSessionHasErrors(['slug']);
        
        // Verify product2 wasn't updated
        $product2->refresh();
        $this->assertEquals('product-two', $product2->slug);
    }

    #[Test]
    public function it_handles_non_existent_product_operations()
    {
        // Try to show non-existent product
        $response = $this->actingAs($this->user)
            ->get('/products/non-existent-uuid');

        $response->assertNotFound();

        // Try to edit non-existent product
        $response = $this->actingAs($this->user)
            ->get('/products/non-existent-uuid/edit');

        $response->assertNotFound();

        // Try to update non-existent product
        $response = $this->actingAs($this->user)
            ->put('/products/non-existent-uuid', [
                'name' => 'Updated Name',
                'price' => 99.99,
            ]);

        $response->assertNotFound();

        // Try to delete non-existent product
        $response = $this->actingAs($this->user)
            ->delete('/products/non-existent-uuid');

        $response->assertNotFound();
    }

    /*
    #[Test]
    public function it_handles_storage_failures_gracefully()
    {
        // Mock storage failure
        Storage::shouldReceive('disk')->with('public')->andReturnSelf();
        Storage::shouldReceive('store')->andReturn(false);

        $image = UploadedFile::fake()->image('product.jpg');
        
        $response = $this->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => 'Product with Storage Failure',
                'slug' => 'product-with-storage-failure',
                'price' => 99.99,
                'image' => $image,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
        $this->assertEquals(0, Product::count());
    }
    */

    #[Test]
    public function it_handles_database_constraint_violations()
    {
        // Create a product to establish a constraint
        $existingProduct = Product::factory()->create(['slug' => 'existing-slug']);

        // Try to create another product with the same slug at the database level
        // This would bypass form validation but hit database constraints
        try {
            Product::create([
                'name' => 'Constraint Violation Product',
                'slug' => 'existing-slug', // This should violate unique constraint
                'price' => 99.99,
            ]);
            
            $this->fail('Expected database constraint violation');
        } catch (\Exception $e) {
            // This is expected - the constraint should prevent duplicate slugs
            $this->assertStringContainsString('unique', strtolower($e->getMessage()));
        }

        // Verify only the original product exists
        $this->assertEquals(1, Product::count());
        $this->assertEquals($existingProduct->id, Product::first()->id);
    }
}
