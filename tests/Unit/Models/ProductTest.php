<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes()
    {
        $fillable = ['name', 'slug', 'price', 'description', 'image_path'];
        
        $this->assertEquals($fillable, (new Product())->getFillable());
    }

    #[Test]
    public function it_casts_price_to_decimal()
    {
        $casts = ['price' => 'decimal:2'];
        
        $this->assertEquals($casts, (new Product())->getCasts());
    }

    #[Test]
    public function it_uses_uuids_as_primary_key()
    {
        $product = new Product();
        
        $this->assertFalse($product->incrementing);
        $this->assertEquals('string', $product->getKeyType());
    }

    #[Test]
    public function it_can_be_created_with_valid_data()
    {
        $productData = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 99.99,
            'description' => 'A test product description',
            'image_path' => 'products/test-image.jpg'
        ];

        $product = Product::create($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($productData['name'], $product->name);
        $this->assertEquals($productData['slug'], $product->slug);
        $this->assertEquals($productData['price'], $product->price);
        $this->assertEquals($productData['description'], $product->description);
        $this->assertEquals($productData['image_path'], $product->image_path);
    }

    #[Test]
    public function it_can_be_created_without_optional_fields()
    {
        $productData = [
            'name' => 'Minimal Product',
            'slug' => 'minimal-product',
            'price' => 49.99,
        ];

        $product = Product::create($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($productData['name'], $product->name);
        $this->assertEquals($productData['slug'], $product->slug);
        $this->assertEquals($productData['price'], $product->price);
        $this->assertNull($product->description);
        $this->assertNull($product->image_path);
    }

    #[Test]
    public function it_automatically_generates_uuid_on_creation()
    {
        $product = Product::factory()->create();

        $this->assertNotNull($product->id);
        $this->assertIsString($product->id);
        $this->assertEquals(36, strlen($product->id)); // UUID length
    }

    #[Test]
    public function it_stores_price_with_correct_precision()
    {
        $product = Product::factory()->create(['price' => 123.456]);

        // Should be rounded to 2 decimal places
        $this->assertEquals('123.46', $product->price);
    }
}
