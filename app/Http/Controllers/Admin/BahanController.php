<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Support\PanelRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BahanController extends Controller
{
    public function index(): View
    {
        $ingredients = Ingredient::orderBy('name')->get();

        return view('admin.data.bahan', compact('ingredients'));
    }

    public function store(Request $request): RedirectResponse
    {
        Ingredient::create($this->validatedData($request));

        return redirect()->route(PanelRoute::name('admin.bahan'))->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $ingredient->update($this->validatedData($request));

        return redirect()->route(PanelRoute::name('admin.bahan'))->with('success', 'Bahan berhasil diperbarui.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        $ingredient->delete();

        return redirect()->route(PanelRoute::name('admin.bahan'))->with('success', 'Bahan berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_quantity' => ['required', 'numeric', 'min:0'],
        ]);

        $data['status'] = $this->statusForQuantity((float) $data['quantity'], (float) $data['minimum_quantity']);

        return $data;
    }

    private function statusForQuantity(float $quantity, float $minimumQuantity): string
    {
        $quantityInThousandths = (int) round($quantity * 1000);
        $minimumInThousandths = (int) round($minimumQuantity * 1000);

        return $quantityInThousandths <= 0
            ? 'critical'
            : ($quantityInThousandths <= $minimumInThousandths ? 'low' : 'available');
    }
}
