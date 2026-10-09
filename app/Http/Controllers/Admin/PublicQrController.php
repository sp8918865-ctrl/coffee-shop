<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicQrController extends Controller
{
    public function index(Request $request): View
    {
        $publicBaseUrl = rtrim(
            config('app.public_menu_url') ?: $request->getSchemeAndHttpHost(),
            '/'
        );

        return view('admin.data.qr', [
            'publicUrl' => $publicBaseUrl.route('catalog.index', [], false),
        ]);
    }
}
