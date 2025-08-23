<?php

namespace Tests\Unit\Actions;

use App\Actions\Product\CreateProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    protected CreateProduct $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateProduct();
        Storage::fake('public');
    }

    #[Test]
    public function it_creates_product_with_basic_data()
    {
        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'A test product',
        ];

        $product = ($this->action)($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('test-product', $product->slug);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals('A test product', $product->description);
        $this->assertNull($product->image_path);
    }

    #[Test]
    public function it_generates_unique_slug_when_slug_exists()
    {
        // Create first product
        Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
        ]);

        $data = [
            'name' => 'Test Product',
            'price' => 89.99,
        ];

        $product = ($this->action)($data);

        $this->assertNotEquals('test-product', $product->slug);
        $this->assertStringStartsWith('test-product-', $product->slug);
    }

    #[Test]
    public function it_creates_product_without_description()
    {
        $data = [
            'name' => 'Simple Product',
            'price' => 29.99,
        ];

        $product = ($this->action)($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Simple Product', $product->name);
        $this->assertEquals(29.99, $product->price);
        $this->assertNull($product->description);
    }

}
