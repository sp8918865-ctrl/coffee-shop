<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankTransferSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.data.bank-transfer', [
            'details' => AppSetting::bankTransferDetails(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $details = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:40', 'regex:/^[0-9 -]+$/'],
            'account_holder' => ['required', 'string', 'max:255'],
        ]);

        AppSetting::setBankTransferDetails($details);

        return redirect()->route('admin.bank-transfer')
            ->with('success', 'Rekening transfer berhasil disimpan.');
    }
}
