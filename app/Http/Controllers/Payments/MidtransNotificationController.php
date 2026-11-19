<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\MidtransGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransGateway $midtrans): JsonResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'string', 'max:50'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'signature_key' => ['required', 'string', 'size:128'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ]);

        abort_unless($midtrans->isConfigured(), 503, 'Payment notification is not configured.');
        abort_unless($midtrans->hasValidSignature($data), 403, 'Invalid payment signature.');

        DB::transaction(function () use ($data): void {
            $payment = Payment::where('payment_gateway_ref', $data['order_id'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                number_format((float) $payment->amount, 2, '.', '') === number_format((float) $data['gross_amount'], 2, '.', ''),
                422,
                'Payment amount does not match the order.'
            );
            abort_unless($payment->payment_method === 'qris', 422, 'Notification does not match a QRIS payment.');

            if ($payment->payment_status === 'paid') {
                return;
            }

            if ($data['transaction_status'] === 'settlement'
                || ($data['transaction_status'] === 'capture' && ($data['fraud_status'] ?? null) === 'accept')) {
                $payment->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
            } elseif (in_array($data['transaction_status'], ['deny', 'cancel', 'expire'], true)) {
                $payment->update(['payment_status' => 'failed']);
            }
        });

        return response()->json(['status' => 'ok']);
    }
}
