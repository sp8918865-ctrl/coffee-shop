<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $categories = Category::whereHas('products', fn ($query) => $query->where('is_available', true))
            ->with(['products' => fn ($query) => $query->where('is_available', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        $uncategorizedProducts = Product::whereNull('category_id')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('catalog.index', compact('categories', 'uncategorizedProducts'));
    }
}
