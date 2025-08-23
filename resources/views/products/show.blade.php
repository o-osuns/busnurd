<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('products.create') }}" 
                   class="bg-green-500 hover:bg-green-600 text-black font-bold py-2 px-4 rounded transition-colors duration-200">
                    Create Product
                </a>
                <a href="{{ route('products.edit', $product) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded transition-colors duration-200">
                    Edit Product
                </a>
                <a href="{{ route('products.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Back to Products
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Menu -->
            <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        <!-- Quick Actions -->
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('products.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create New Product
                            </a>
                            <a href="{{ route('products.edit', $product) }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit This Product
                            </a>
                            <a href="{{ route('products.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                                </svg>
                                Back to All Products
                            </a>
                        </div>

                        <!-- Product Status -->
                        <div class="flex items-center space-x-3">
                            <div class="text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active Product
                                </span>
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Product Image -->
                        <div class="w-full">
                            <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="text-gray-400 text-center">
                                        <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-lg mt-4">No Image Available</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="w-full">
                            <div class="space-y-6">
                                <!-- Product Name -->
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                                        {{ $product->name }}
                                    </h1>
                                    <p class="text-sm text-gray-500">
                                        Slug: {{ $product->slug }}
                                    </p>
                                </div>

                                <!-- Price -->
                                <div>
                                    <p class="text-4xl font-bold text-green-600">
                                        ${{ number_format($product->price, 2) }}
                                    </p>
                                </div>

                                <!-- Description -->
                                @if($product->description)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                                    <div class="prose max-w-none">
                                        <p class="text-gray-700 leading-relaxed">
                                            {{ $product->description }}
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <!-- Product Information -->
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Information</h3>
                                    <dl class="grid grid-cols-1 gap-4">
                                        <div class="flex justify-between">
                                            <dt class="font-medium text-gray-500">Product ID:</dt>
                                            <dd class="text-gray-900 font-mono text-sm">{{ $product->id }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="font-medium text-gray-500">Created:</dt>
                                            <dd class="text-gray-900">{{ $product->created_at->format('M d, Y \a\t g:i A') }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="font-medium text-gray-500">Last Updated:</dt>
                                            <dd class="text-gray-900">{{ $product->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Action Buttons -->
                                <div class="border-t pt-6">
                                    <div class="flex space-x-4">
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-3 px-6 rounded-lg font-medium transition-colors duration-200">
                                            Edit Product
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" 
                                              method="POST" 
                                              class="flex-1"
                                              onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full bg-red-500 hover:bg-red-600 text-white py-3 px-6 rounded-lg font-medium transition-colors duration-200">
                                                Delete Product
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
