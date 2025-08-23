<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductFactoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_product_with_factory()
    {
        $product = Product::factory()->create();

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNotNull($product->id);
        $this->assertNotNull($product->name);
        $this->assertNotNull($product->slug);
        $this->assertNotNull($product->price);
        $this->assertNull($product->image_path); // Default null
    }

    #[Test]
    public function it_generates_unique_slugs()
    {
        $products = Product::factory()->count(5)->create();

        $slugs = $products->pluck('slug')->toArray();
        $uniqueSlugs = array_unique($slugs);

        $this->assertCount(5, $uniqueSlugs);
    }

    #[Test]
    public function it_generates_valid_price_range()
    {
        $products = Product::factory()->count(10)->create();

        foreach ($products as $product) {
            $this->assertGreaterThanOrEqual(5, $product->price);
            $this->assertLessThanOrEqual(500, $product->price);
        }
    }

    #[Test]
    public function it_generates_description()
    {
        $product = Product::factory()->create();

        $this->assertNotNull($product->description);
        $this->assertIsString($product->description);
    }

    #[Test]
    public function it_creates_multiple_products()
    {
        $products = Product::factory()->count(3)->create();

        $this->assertCount(3, $products);
        $this->assertEquals(3, Product::count());
    }

    #[Test]
    public function it_makes_product_without_saving()
    {
        $product = Product::factory()->make();

        $this->assertInstanceOf(Product::class, $product);
        $this->assertNull($product->id); // Not saved to database
        $this->assertEquals(0, Product::count());
    }
}
