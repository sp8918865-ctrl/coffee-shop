<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Support\PanelRoute;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
            'isEnabled' => AppSetting::isPublicQrEnabled(),
        ]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        AppSetting::setPublicQrEnabled((bool) $data['enabled']);

        return redirect()->route(PanelRoute::name('admin.qr-menu'))->with(
            'success',
            $data['enabled'] ? 'QR menu publik diaktifkan.' : 'QR menu publik dinonaktifkan.'
        );
    }
}
