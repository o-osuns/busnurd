<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductMigrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_products_table_with_correct_structure()
    {
        $this->assertTrue(Schema::hasTable('products'));
    }

    #[Test]
    public function it_has_correct_columns()
    {
        $columns = [
            'id',
            'name',
            'slug',
            'price',
            'description',
            'image_path',
            'created_at',
            'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('products', $column),
                "Column {$column} does not exist in products table"
            );
        }
    }

    #[Test]
    public function it_has_primary_key_on_id()
    {
        $indexes = Schema::getIndexes('products');
        $primaryKey = collect($indexes)->firstWhere('primary', true);
        
        $this->assertNotNull($primaryKey);
        $this->assertEquals(['id'], $primaryKey['columns']);
    }

    #[Test]
    public function it_has_unique_index_on_slug()
    {
        $indexes = Schema::getIndexes('products');
        $uniqueIndex = collect($indexes)->firstWhere('unique', true);
        
        $this->assertNotNull($uniqueIndex);
        $this->assertContains('slug', $uniqueIndex['columns']);
    }

    #[Test]
    public function it_has_index_on_name()
    {
        $indexes = Schema::getIndexes('products');
        $nameIndex = collect($indexes)->first(function ($index) {
            return in_array('name', $index['columns']) && !$index['primary'] && !$index['unique'];
        });
        
        $this->assertNotNull($nameIndex);
    }
}
