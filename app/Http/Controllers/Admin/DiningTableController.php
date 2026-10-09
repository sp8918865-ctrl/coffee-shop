<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DiningTableController extends Controller
{
    public function index(): View
    {
        $tables = DiningTable::withCount('orders')->orderBy('table_number')->get();

        return view('admin.data.tables', compact('tables'));
    }

    public function status(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'tables' => DiningTable::query()
                ->orderBy('table_number')
                ->get(['table_id', 'status'])
                ->map(fn (DiningTable $table): array => [
                    'id' => $table->table_id,
                    'status' => $table->status,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        DiningTable::create($this->validatedData($request));

        return redirect()->route('admin.tables')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function update(Request $request, DiningTable $table): RedirectResponse
    {
        $table->update($this->validatedData($request, $table));

        return redirect()->route('admin.tables')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        $table->delete();

        return redirect()->route('admin.tables')->with('success', 'Meja berhasil dihapus.');
    }

    private function validatedData(Request $request, ?DiningTable $table = null): array
    {
        return $request->validate([
            'table_number' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tables', 'table_number')->ignore($table?->table_id, 'table_id'),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', 'in:available,occupied,reserved'],
        ]);
    }
}
