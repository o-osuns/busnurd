<?php

namespace Tests\Unit\Actions;

use App\Actions\Product\UpdateProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    protected UpdateProduct $action;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateProduct();
        Storage::fake('public');
        
        $this->product = Product::factory()->create([
            'name' => 'Original Product',
            'slug' => 'original-product',
            'price' => 99.99,
            'description' => 'Original description',
        ]);
    }

    #[Test]
    public function it_updates_product_basic_data()
    {
        $data = [
            'name' => 'Updated Product',
            'price' => 149.99,
            'description' => 'Updated description',
        ];

        $updatedProduct = ($this->action)($this->product, $data);

        $this->assertEquals('Updated Product', $updatedProduct->name);
        $this->assertEquals(149.99, $updatedProduct->price);
        $this->assertEquals('Updated description', $updatedProduct->description);
    }

    #[Test]
    public function it_updates_slug_when_name_changes()
    {
        $originalSlug = $this->product->slug;
        
        $data = [
            'name' => 'Completely New Name',
            'price' => $this->product->price,
        ];

        $updatedProduct = ($this->action)($this->product, $data);

        $this->assertEquals('completely-new-name', $updatedProduct->slug);
        $this->assertNotEquals($originalSlug, $updatedProduct->slug);
    }

    #[Test]
    public function it_keeps_slug_when_name_unchanged()
    {
        $originalSlug = $this->product->slug;
        
        $data = [
            'name' => $this->product->name,
            'price' => 199.99,
        ];

        $updatedProduct = ($this->action)($this->product, $data);

        $this->assertEquals($originalSlug, $updatedProduct->slug);
        $this->assertEquals(199.99, $updatedProduct->price);
    }

    #[Test]
    public function it_generates_unique_slug_when_name_conflicts()
    {
        // Create another product with a conflicting slug
        Product::factory()->create([
            'name' => 'Existing Product',
            'slug' => 'existing-product',
        ]);

        $data = [
            'name' => 'Existing Product',
            'price' => $this->product->price,
        ];

        $updatedProduct = ($this->action)($this->product, $data);

        $this->assertNotEquals('existing-product', $updatedProduct->slug);
        $this->assertStringStartsWith('existing-product-', $updatedProduct->slug);
    }

    #[Test]
    public function it_updates_without_changing_image()
    {
        $originalImagePath = 'products/original-image.jpg';
        $this->product->update(['image_path' => $originalImagePath]);

        $data = [
            'name' => 'Updated Name',
            'price' => 299.99,
            'description' => 'Updated without image change',
        ];

        $updatedProduct = ($this->action)($this->product, $data);

        $this->assertEquals($originalImagePath, $updatedProduct->image_path);
        $this->assertEquals('Updated Name', $updatedProduct->name);
        $this->assertEquals(299.99, $updatedProduct->price);
    }

    #[Test]
    public function it_can_clear_description()
    {
        $data = [
            'name' => $this->product->name,
            'price' => $this->product->price,
            'description' => null,
        ];

        $updatedProduct = ($this->action)($this->product, $data);

        $this->assertNull($updatedProduct->description);
    }

    #[Test]
    public function it_generates_unique_slug_correctly()
    {
        $action = new UpdateProduct();
        $method = new \ReflectionMethod($action, 'generateUniqueSlug');
        $method->setAccessible(true);

        // Test normal case
        $slug = $method->invoke($action, 'Test Product', $this->product->id);
        $this->assertEquals('test-product', $slug);

        // Test conflict case
        Product::factory()->create(['slug' => 'test-product']);
        $slug = $method->invoke($action, 'Test Product', $this->product->id);
        $this->assertNotEquals('test-product', $slug);
        $this->assertStringStartsWith('test-product-', $slug);
    }
}
