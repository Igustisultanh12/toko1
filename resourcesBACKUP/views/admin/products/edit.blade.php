<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Produk: ') }} {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Arahkan form ke route update dan gunakan method PUT --}}
                    <form action="{{ route('admin.products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Produk -->
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Nama Produk</label>
                                {{-- Isi value dengan data produk yang ada --}}
                                <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('name', $product->name) }}" required>
                            </div>

                            <!-- Barcode -->
                            <div>
                                <label for="barcode" class="block font-medium text-sm text-gray-700">Barcode</label>
                                <input type="text" name="barcode" id="barcode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('barcode', $product->barcode) }}">
                            </div>

                            <!-- Harga -->
                            <div>
                                <label for="price" class="block font-medium text-sm text-gray-700">Harga</label>
                                <input type="number" name="price" id="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('price', $product->price) }}" required>
                            </div>

                            <!-- Stok -->
                            <div>
                                <label for="stock" class="block font-medium text-sm text-gray-700">Stok</label>
                                <input type="number" name="stock" id="stock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('stock', $product->stock) }}" required>
                            </div>

                            <!-- Diskon -->
                            <div>
                                <label for="discount_percent" class="block font-medium text-sm text-gray-700">Diskon (%)</label>
                                <input type="number" name="discount_percent" id="discount_percent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('discount_percent', $product->discount_percent) }}">
                            </div>

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi</label>
                                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Update Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
