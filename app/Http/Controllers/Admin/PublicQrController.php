<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PublicQrController extends Controller
{
    public function index(Request $request): View
    {
        $publicBaseUrl = rtrim(
            config('app.public_menu_url') ?: $request->getSchemeAndHttpHost(),
            '/'
        );

        $qrEntryPath = URL::signedRoute('customer.qr.enter', absolute: false);
        $apiQrEntryPath = URL::signedRoute('api.v1.customer.qr-access', absolute: false);

        return view('admin.data.qr', [
            'publicUrl' => $publicBaseUrl.$qrEntryPath,
            'apiUrl' => $publicBaseUrl.$apiQrEntryPath,
        ]);
    }
}
