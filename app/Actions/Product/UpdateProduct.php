<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateProduct
{
    public function __invoke(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            if ($product->name !== $data['name']) {
                $product->slug = $this->generateUniqueSlug($data['name'], $product->id);
            }

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $newImagePath = $data['image']->store('products', 'public');
                
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                
                $product->image_path = $newImagePath;
            }

            $product->fill([
                'name'        => $data['name'],
                'price'       => $data['price'],
                'description' => $data['description'] ?? null,
            ])->save();

            return $product;
        });
    }

    private function generateUniqueSlug(string $name, string $excludeId): string
    {
        $slug = Str::slug($name);
        
        if (Product::where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug .= '-' . Str::lower(Str::ulid());
        }
        
        return $slug;
    }
}