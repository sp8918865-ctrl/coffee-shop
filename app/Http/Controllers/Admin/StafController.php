<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StafController extends Controller
{
    public function index(): View
    {
        $stafs = User::orderBy('name')->get();
        $roles = [
            'barista' => 'Barista',
            'cashier' => 'Kasir',
            'admin' => 'Admin',
        ];

        return view('admin.data.staf', compact('stafs', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['nullable', Rule::in(['barista', 'cashier', 'admin'])],
        ]);

        $data['role'] = $data['role'] ?? 'barista';
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);
        User::create($data);

        return redirect()->route('admin.staf')->with('success', 'Data staf berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => [
                'sometimes',
                Rule::in($user->role === 'owner'
                    ? ['barista', 'cashier', 'admin', 'owner']
                    : ['barista', 'cashier', 'admin']),
            ],
        ]);

        if (filled($data['password'] ?? null)) {
            $data['password_hash'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['password']);

        $user->update($data);

        return redirect()->route('admin.staf')->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.staf')->with('success', 'Data staf berhasil dihapus.');
    }
}
