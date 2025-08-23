<?php

namespace Tests\Feature;

use App\Actions\Product\CreateProduct;
use App\Actions\Product\DeleteProduct;
use App\Actions\Product\UpdateProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductActionsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /*
    #[Test]
    public function it_completes_full_product_lifecycle()
    {
        // Create
        $createAction = new CreateProduct();
        $createData = [
            'name' => 'Lifecycle Test Product',
            'price' => 99.99,
            'description' => 'A product for testing the complete lifecycle',
            'image' => UploadedFile::fake()->image('product.jpg'),
        ];

        $product = $createAction($createData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Lifecycle Test Product', $product->name);
        $this->assertEquals('lifecycle-test-product', $product->slug);
        $this->assertNotNull($product->image_path);
        $this->assertTrue(Storage::disk('public')->exists($product->image_path));

        // Update
        $updateAction = new UpdateProduct();
        $updateData = [
            'name' => 'Updated Lifecycle Product',
            'price' => 149.99,
            'description' => 'Updated description',
            'image' => UploadedFile::fake()->image('updated-product.jpg'),
        ];

        $oldImagePath = $product->image_path;
        $updatedProduct = $updateAction($product, $updateData);

        $this->assertEquals('Updated Lifecycle Product', $updatedProduct->name);
        $this->assertEquals('updated-lifecycle-product', $updatedProduct->slug);
        $this->assertEquals(149.99, $updatedProduct->price);
        $this->assertNotEquals($oldImagePath, $updatedProduct->image_path);
        $this->assertFalse(Storage::disk('public')->exists($oldImagePath));
        $this->assertTrue(Storage::disk('public')->exists($updatedProduct->image_path));

        // Delete
        $deleteAction = new DeleteProduct();
        $deleteAction($updatedProduct);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    #[Test]
    public function it_handles_image_operations_correctly()
    {
        $createAction = new CreateProduct();
        $updateAction = new UpdateProduct();

        // Create product with image
        $image1 = UploadedFile::fake()->image('image1.jpg');
        $product = $createAction([
            'name' => 'Image Test Product',
            'price' => 99.99,
            'image' => $image1,
        ]);

        $originalImagePath = $product->image_path;
        $this->assertTrue(Storage::disk('public')->exists($originalImagePath));

        // Update with new image
        $image2 = UploadedFile::fake()->image('image2.jpg');
        $updatedProduct = $updateAction($product, [
            'name' => $product->name,
            'price' => $product->price,
            'image' => $image2,
        ]);

        // Old image should be deleted, new image should exist
        $this->assertFalse(Storage::disk('public')->exists($originalImagePath));
        $this->assertTrue(Storage::disk('public')->exists($updatedProduct->image_path));
        $this->assertNotEquals($originalImagePath, $updatedProduct->image_path);

        // Update without changing image
        $productWithoutImageChange = $updateAction($updatedProduct, [
            'name' => 'Name Changed Only',
            'price' => 199.99,
        ]);

        // Image should remain the same
        $this->assertEquals($updatedProduct->image_path, $productWithoutImageChange->image_path);
        $this->assertTrue(Storage::disk('public')->exists($productWithoutImageChange->image_path));
    }
    */

    #[Test]
    public function it_handles_slug_conflicts_correctly()
    {
        $createAction = new CreateProduct();

        // Create first product
        $product1 = $createAction([
            'name' => 'Duplicate Name Product',
            'price' => 99.99,
        ]);

        $this->assertEquals('duplicate-name-product', $product1->slug);

        // Create second product with same name
        $product2 = $createAction([
            'name' => 'Duplicate Name Product',
            'price' => 149.99,
        ]);

        $this->assertNotEquals($product1->slug, $product2->slug);
        $this->assertStringStartsWith('duplicate-name-product-', $product2->slug);

        // Update to create slug conflict
        $updateAction = new UpdateProduct();
        $product3 = $createAction([
            'name' => 'Another Product',
            'price' => 199.99,
        ]);

        $updatedProduct3 = $updateAction($product3, [
            'name' => 'Duplicate Name Product',
            'price' => $product3->price,
        ]);

        $this->assertNotEquals($product1->slug, $updatedProduct3->slug);
        $this->assertNotEquals($product2->slug, $updatedProduct3->slug);
        $this->assertStringStartsWith('duplicate-name-product-', $updatedProduct3->slug);
    }

    #[Test]
    public function it_maintains_data_integrity_across_operations()
    {
        $createAction = new CreateProduct();
        $updateAction = new UpdateProduct();

        // Create multiple products
        $products = [];
        for ($i = 1; $i <= 5; $i++) {
            $products[] = $createAction([
                'name' => "Product {$i}",
                'price' => $i * 10,
                'description' => "Description for product {$i}",
            ]);
        }

        $this->assertEquals(5, Product::count());

        // Update middle product
        $updatedProduct = $updateAction($products[2], [
            'name' => 'Updated Product 3',
            'price' => 999.99,
        ]);

        // Verify all other products remain unchanged
        foreach ($products as $index => $product) {
            if ($index === 2) {
                continue; // Skip the updated one
            }
            
            $product->refresh();
            $this->assertEquals("Product " . ($index + 1), $product->name);
            $this->assertEquals(($index + 1) * 10, $product->price);
        }

        // Verify updated product
        $this->assertEquals('Updated Product 3', $updatedProduct->name);
        $this->assertEquals(999.99, $updatedProduct->price);
        $this->assertEquals('updated-product-3', $updatedProduct->slug);
    }

    #[Test]
    public function it_handles_edge_cases_gracefully()
    {
        $createAction = new CreateProduct();
        $updateAction = new UpdateProduct();

        // Test with minimal data
        $minimalProduct = $createAction([
            'name' => 'Minimal',
            'price' => 0.01,
        ]);

        $this->assertEquals('Minimal', $minimalProduct->name);
        $this->assertEquals(0.01, $minimalProduct->price);
        $this->assertEquals('minimal', $minimalProduct->slug);
        $this->assertNull($minimalProduct->description);
        $this->assertNull($minimalProduct->image_path);

        // Test with maximum valid price
        $expensiveProduct = $createAction([
            'name' => 'Expensive Product',
            'price' => 99999999.99,
        ]);

        $this->assertEquals(99999999.99, $expensiveProduct->price);

        // Test updating with null description
        $updatedMinimal = $updateAction($minimalProduct, [
            'name' => $minimalProduct->name,
            'price' => $minimalProduct->price,
            'description' => null,
        ]);

        $this->assertNull($updatedMinimal->description);
    }
}
