<?php

namespace App\Actions\Product;

use App\Models\Product;

class DeleteProduct
{
    public function __invoke(Product $product): void
    {
        $product->delete();
    }
}