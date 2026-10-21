<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $categories = Category::whereHas('products', fn ($query) => $query->availableForSale())
            ->with(['products' => fn ($query) => $query->availableForSale()->orderBy('name')])
            ->orderBy('name')
            ->get();

        $uncategorizedProducts = Product::availableForSale()
            ->whereNull('category_id')
            ->orderBy('name')
            ->get();

        return view('catalog.index', compact('categories', 'uncategorizedProducts'));
    }
}
