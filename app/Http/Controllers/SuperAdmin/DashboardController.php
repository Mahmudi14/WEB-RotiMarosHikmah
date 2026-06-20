<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('super-admin.dashboard', [
            'totalUsers' => User::count(),
            'totalSuperAdmins' => User::where('role', 'super_admin')->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalCashiers' => User::where('role', 'kasir')->count(),
            'totalFinance' => User::where('role', 'keuangan')->count(),

        ]);
    }
}