<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
            ->when($request->boolean('unavailable'), fn ($query) => $query->where('is_available', false))
            ->orderBy('name')
            ->get();
        $categories = Category::withCount('products')->orderBy('name')->get();
        $totalProducts = Product::count();
        $availableProducts = Product::where('is_available', true)->count();
        $unavailableProducts = Product::where('is_available', false)->count();

        return view('admin.data.menu', compact(
            'products', 'categories', 'totalProducts', 'availableProducts', 'unavailableProducts'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validatedData($request));

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validatedData($request, $product));

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil dihapus.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::create($data);

        return redirect()->route('admin.menu')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.menu')->with('success', 'Kategori berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]) + ['is_available' => $request->boolean('is_available')];
    }
}
