<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $role = $request->user()->role;

        return match ($role) {
            'kasir' => redirect()->route('cashier.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'keuangan' => redirect()->route('finance.dashboard'),
            'super_admin' => redirect()->route('super-admin.dashboard'),
            default => abort(403, 'Role pengguna belum ditentukan.'),
        };
    }
}