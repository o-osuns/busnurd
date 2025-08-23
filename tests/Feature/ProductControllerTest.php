<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductControllerTest extends TestCase
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
    public function it_displays_products_index_page()
    {
        $products = Product::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->get(route('products.index'));

        $response->assertOk();
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
        
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    #[Test]
    public function it_paginates_products_on_index_page()
    {
        Product::factory()->count(25)->create();

        $response = $this->actingAs($this->user)
            ->get(route('products.index'));

        $response->assertOk();
        $response->assertViewHas('products');
        
        $products = $response->viewData('products');
        $this->assertEquals(20, $products->count()); // Default per page
    }

    #[Test]
    public function it_accepts_custom_per_page_parameter()
    {
        Product::factory()->count(15)->create();

        $response = $this->actingAs($this->user)
            ->get(route('products.index', ['per_page' => 10]));

        $response->assertOk();
        $products = $response->viewData('products');
        $this->assertEquals(10, $products->count());
    }

    #[Test]
    public function it_limits_per_page_parameter()
    {
        Product::factory()->count(150)->create();

        // Test maximum limit
        $response = $this->actingAs($this->user)
            ->get(route('products.index', ['per_page' => 200]));

        $products = $response->viewData('products');
        $this->assertEquals(100, $products->count()); // Should be limited to 100

        // Test minimum limit
        $response = $this->actingAs($this->user)
            ->get(route('products.index', ['per_page' => -5]));

        $products = $response->viewData('products');
        $this->assertEquals(1, $products->count()); // Should be at least 1
    }

    #[Test]
    public function it_shows_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('products.show', $product));

        $response->assertOk();
        $response->assertViewIs('products.show');
        $response->assertViewHas('product', $product);
        $response->assertSee($product->name);
    }

    #[Test]
    public function it_shows_create_product_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('products.create'));

        $response->assertOk();
        $response->assertViewIs('products.create');
    }

    #[Test]
    public function it_creates_product_with_valid_data()
    {
        $productData = [
            'name' => 'New Product',
            'slug' => 'new-product',
            'price' => 99.99,
            'description' => 'A new product description',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('products.store'), $productData);

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'slug' => 'new-product',
            'price' => 99.99,
        ]);

        $product = Product::where('slug', 'new-product')->first();
        $response->assertRedirect(route('products.show', $product));
        $response->assertSessionHas('status', 'Product created successfully.');
    }

    /*
    #[Test]
    public function it_creates_product_with_image()
    {
        $image = UploadedFile::fake()->image('product.jpg');
        
        $productData = [
            'name' => 'Product with Image',
            'slug' => 'product-with-image',
            'price' => 149.99,
            'image' => $image,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('products.store'), $productData);

        $product = Product::where('slug', 'product-with-image')->first();
        
        $this->assertNotNull($product->image_path);
        $this->assertTrue(Storage::disk('public')->exists($product->image_path));
        
        $response->assertRedirect(route('products.show', $product));
    }
    */

    #[Test]
    public function it_validates_product_creation_data()
    {
        $response = $this->actingAs($this->user)
            ->post(route('products.store'), [
                'name' => '', // Invalid: required field
                'price' => 'not-a-number', // Invalid: not numeric
            ]);

        $response->assertSessionHasErrors(['name', 'slug', 'price']);
        $this->assertEquals(0, Product::count());
    }

    #[Test]
    public function it_shows_edit_product_form()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('products.edit', $product));

        $response->assertOk();
        $response->assertViewIs('products.edit');
        $response->assertViewHas('product', $product);
    }

    #[Test]
    public function it_updates_product_with_valid_data()
    {
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 99.99,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'price' => 149.99,
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('products.update', $product), $updateData);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 149.99,
        ]);

        $response->assertRedirect(route('products.show', $product));
        $response->assertSessionHas('status', 'Product updated successfully.');
    }

    /*
    #[Test]
    public function it_updates_product_with_new_image()
    {
        $product = Product::factory()->create();
        $newImage = UploadedFile::fake()->image('new-product.jpg');

        $updateData = [
            'name' => $product->name,
            'price' => $product->price,
            'image' => $newImage,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('products.update', $product), $updateData);

        $product->refresh();
        $this->assertNotNull($product->image_path);
        $this->assertTrue(Storage::disk('public')->exists($product->image_path));

        $response->assertRedirect(route('products.show', $product));
    }
    */

    #[Test]
    public function it_validates_product_update_data()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('products.update', $product), [
                'price' => 'not-a-number', // Invalid
            ]);

        $response->assertSessionHasErrors(['price']);
    }

    #[Test]
    public function it_deletes_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('products.destroy', $product));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('status', 'Product deleted.');
    }

    #[Test]
    public function it_requires_authentication_for_all_routes()
    {
        $product = Product::factory()->create();

        // Test all routes require authentication
        $this->get(route('products.index'))->assertRedirect(route('login'));
        $this->get(route('products.show', $product))->assertRedirect(route('login'));
        $this->get(route('products.create'))->assertRedirect(route('login'));
        $this->post(route('products.store'))->assertRedirect(route('login'));
        $this->get(route('products.edit', $product))->assertRedirect(route('login'));
        $this->put(route('products.update', $product))->assertRedirect(route('login'));
        $this->delete(route('products.destroy', $product))->assertRedirect(route('login'));
    }

    #[Test]
    public function it_returns_404_for_non_existent_product()
    {
        $response = $this->actingAs($this->user)
            ->get('/products/non-existent-id');

        $response->assertNotFound();
    }

    #[Test]
    public function it_orders_products_by_latest_on_index()
    {
        $oldProduct = Product::factory()->create(['created_at' => now()->subDays(2)]);
        $newProduct = Product::factory()->create(['created_at' => now()]);

        $response = $this->actingAs($this->user)
            ->get(route('products.index'));

        $products = $response->viewData('products');
        $this->assertEquals($newProduct->id, $products->first()->id);
    }
}
