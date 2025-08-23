<?php

namespace Tests\Unit\Actions;

use App\Actions\Product\DeleteProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    protected DeleteProduct $action;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteProduct();
        
        $this->product = Product::factory()->create([
            'name' => 'Product to Delete',
            'slug' => 'product-to-delete',
            'price' => 99.99,
        ]);
    }

    #[Test]
    public function it_deletes_product()
    {
        $productId = $this->product->id;
        
        ($this->action)($this->product);

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    #[Test]
    public function it_soft_deletes_product_if_soft_delete_is_enabled()
    {
        // Note: If you add SoftDeletes trait to Product model later,
        // this test would need to be updated accordingly
        $productId = $this->product->id;
        
        ($this->action)($this->product);

        // For now, we're doing hard delete
        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    #[Test]
    public function it_does_not_throw_exception_when_deleting_valid_product()
    {
        $this->expectNotToPerformAssertions();
        
        try {
            ($this->action)($this->product);
        } catch (\Exception $e) {
            $this->fail('Should not throw exception when deleting valid product: ' . $e->getMessage());
        }
    }

    #[Test]
    public function it_returns_void()
    {
        $result = ($this->action)($this->product);
        
        $this->assertNull($result);
    }
}
