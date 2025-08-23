<?php

namespace App\Http\Controllers;

use App\Actions\Product\CreateProduct;
use App\Actions\Product\DeleteProduct;
use App\Actions\Product\UpdateProduct;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $perPage = min(max((int)$perPage, 1), 100);
        
        $products = Product::latest()->paginate($perPage);
        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(ProductStoreRequest $request, CreateProduct $action)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }
            
            $product = $action($data);
            return redirect()->route('products.show', $product)->with('status', 'Product created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create product. Please try again.']);
        }
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductUpdateRequest $request, Product $product, UpdateProduct $action)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }
            
            $action($product, $data);
            return redirect()->route('products.show', $product)->with('status', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update product. Please try again.']);
        }
    }

    public function destroy(Product $product, DeleteProduct $action)
    {
        $action($product);
        return redirect()->route('products.index')->with('status', 'Product deleted.');
    }
}
