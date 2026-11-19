<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferRecipient;
use App\Support\PanelRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransferRecipientController extends Controller
{
    public function index(): View
    {
        return view('admin.data.transfer-recipient', [
            'recipients' => TransferRecipient::orderBy('bank')->orderBy('account_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        TransferRecipient::create($this->validatedData($request));

        return redirect()->route(PanelRoute::name('admin.transfer-recipient'))
            ->with('success', 'Penerima transfer berhasil ditambahkan.');
    }

    public function update(Request $request, TransferRecipient $recipient): RedirectResponse
    {
        $recipient->update($this->validatedData($request));

        return redirect()->route(PanelRoute::name('admin.transfer-recipient'))
            ->with('success', 'Data penerima transfer berhasil diperbarui.');
    }

    public function destroy(TransferRecipient $recipient): RedirectResponse
    {
        $recipient->delete();

        return redirect()->route(PanelRoute::name('admin.transfer-recipient'))
            ->with('success', 'Penerima transfer berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'bank' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
        ]);
    }
}
