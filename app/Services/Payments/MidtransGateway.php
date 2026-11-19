<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MidtransGateway
{
    public function isConfigured(): bool
    {
        return filled(config('payments.midtrans.server_key'));
    }

    public function createQrisPayment(Order $order, Payment $payment): void
    {
        $serverKey = config('payments.midtrans.server_key');
        if (! is_string($serverKey) || trim($serverKey) === '') {
            throw new MidtransGatewayException('Midtrans server key is not configured.');
        }

        $gatewayOrderId = $order->order_number.'-'.$payment->payment_id.'-'.Str::upper(Str::random(6));
        $payment->update([
            'payment_gateway_ref' => $gatewayOrderId,
            'payment_redirect_url' => null,
        ]);

        $trackingPath = URL::signedRoute(
            'customer.order.track.enter',
            ['token' => $order->public_tracking_token],
            absolute: false
        );
        $publicBaseUrl = rtrim(
            config('app.public_menu_url') ?: request()->getSchemeAndHttpHost(),
            '/'
        );

        $payload = [
            'transaction_details' => [
                'order_id' => $gatewayOrderId,
                'gross_amount' => (int) round((float) $payment->amount),
            ],
            'enabled_payments' => ['qris'],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_phone,
            ],
            'callbacks' => [
                'finish' => $publicBaseUrl.$trackingPath,
            ],
        ];

        try {
            $response = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout(15)
                ->post(rtrim(config('payments.midtrans.base_url'), '/').'/snap/v1/transactions', $payload);
        } catch (ConnectionException $exception) {
            throw new MidtransGatewayException('Midtrans could not be reached.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new MidtransGatewayException('Midtrans rejected the QRIS payment request (HTTP '.$response->status().').');
        }

        $redirectUrl = $response->json('redirect_url');
        $redirectHost = is_string($redirectUrl) ? parse_url($redirectUrl, PHP_URL_HOST) : null;
        if (! is_string($redirectUrl)
            || ! in_array($redirectHost, ['app.midtrans.com', 'app.sandbox.midtrans.com'], true)
            || parse_url($redirectUrl, PHP_URL_SCHEME) !== 'https') {
            throw new MidtransGatewayException('Midtrans returned an invalid payment URL.');
        }

        $payment->update(['payment_redirect_url' => $redirectUrl]);
    }

    public function hasValidSignature(array $notification): bool
    {
        $serverKey = config('payments.midtrans.server_key');
        if (! is_string($serverKey) || trim($serverKey) === '') {
            return false;
        }

        $signature = hash(
            'sha512',
            $notification['order_id'].$notification['status_code'].$notification['gross_amount'].$serverKey
        );

        return hash_equals($signature, $notification['signature_key']);
    }
}
