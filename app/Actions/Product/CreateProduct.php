<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateProduct
{
    public function __invoke(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $slug = Str::slug($data['name']);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::lower(Str::ulid());
            }

            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                try {
                    $imagePath = $data['image']->store('products', 'public');
                    if (!$imagePath) {
                        throw new \RuntimeException('Failed to store product image');
                    }
                } catch (\Exception $e) {
                    throw new \RuntimeException('Image upload failed: ' . $e->getMessage());
                }
            }

            try {
                return Product::create([
                    'name'        => $data['name'],
                    'slug'        => $slug,
                    'price'       => $data['price'],
                    'description' => $data['description'] ?? null,
                    'image_path'  => $imagePath,
                ]);
            } catch (\Exception $e) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                throw new \RuntimeException('Product creation failed: ' . $e->getMessage());
            }
        });
    }
}
