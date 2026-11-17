<?php

namespace app\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class donation extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:1',
            'donor_name' => 'nullable|string|max:255',
            'donor_email' => 'nullable|email|max:255',
        ]);

        $transaction = Transaction::create([
            'amount' => $validatedData['amount'],
            'donor_name' => $validatedData['donor_name'] ?? null,
            'donor_email' => $validatedData['donor_email'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('donation.thankyou');
    }
}
