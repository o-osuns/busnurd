<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            <a href="{{ route('products.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                Add New Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Menu -->
            <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        <!-- Quick Actions -->
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('products.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create New Product
                            </a>
                            <a href="{{ route('products.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                All Products
                            </a>
                        </div>

                        <!-- Search and Filter -->
                        <div class="flex items-center space-x-3">
                            <div class="text-sm text-gray-600">
                                @if($products->count() > 0)
                                    {{ $products->total() }} {{ Str::plural('product', $products->total()) }} total
                                @else
                                    No products
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($products->count() > 0)
                        <!-- Products Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($products as $product)
                                <!-- Clickable Product Card -->
                                <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden hover:shadow-xl hover:border-blue-300 transition-all duration-300 transform hover:-translate-y-1">
                                    <!-- Clickable product info area -->
                                    <a href="{{ route('products.show', $product) }}" class="block">
                                        <!-- Product Image -->
                                        <div class="h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                                            @if($product->image_path)
                                                <img src="{{ asset('storage/' . $product->image_path) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="h-full w-full object-cover hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="text-gray-400 text-center">
                                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-sm mt-2">No Image</p>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Details -->
                                        <div class="p-4">
                                            <h3 class="font-semibold text-lg text-gray-800 mb-2 truncate hover:text-blue-600 transition-colors duration-200">
                                                {{ $product->name }}
                                            </h3>
                                            
                                            <p class="text-xl font-bold text-green-600 mb-2">
                                                ${{ number_format($product->price, 2) }}
                                            </p>
                                            
                                            @if($product->description)
                                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                                    {{ Str::limit($product->description, 100) }}
                                                </p>
                                            @endif

                                            <!-- Click to view hint -->
                                            <div class="text-xs text-blue-500 mb-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Click to view details
                                            </div>
                                        </div>
                                    </a>

                                    <!-- Action Buttons (outside the clickable area) -->
                                    <div class="px-4 pb-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="flex-1 bg-blue-500 hover:bg-blue-600 text-black text-center py-2 px-3 rounded text-sm font-medium transition-colors duration-200">
                                                View
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}" 
                                               class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-black text-center py-2 px-3 rounded text-sm font-medium transition-colors duration-200"
                                               onclick="event.stopPropagation();">
                                                Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" 
                                                  method="POST" 
                                                  class="flex-1"
                                                  onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full bg-red-500 hover:bg-red-600 text-black py-2 px-3 rounded text-sm font-medium transition-colors duration-200">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>

                        <!-- Products Per Page Selector -->
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                            </div>
                            
                            <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-2">
                                <label for="per_page" class="text-sm text-gray-600">Items per page:</label>
                                <select name="per_page" id="per_page" onchange="this.form.submit()" 
                                        class="border border-gray-300 rounded px-2 py-1 text-sm">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </form>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="text-xl font-medium text-gray-700 mb-2">No products found</h3>
                            <p class="text-gray-500 mb-6">Get started by creating your first product.</p>
                            <a href="{{ route('products.create') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                Create Your First Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
