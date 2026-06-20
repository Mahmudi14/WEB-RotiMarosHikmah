<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $adminDashboardService
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'todaySummary' => $this->adminDashboardService->getTodaySummary(),
            'shiftSummary' => $this->adminDashboardService->getShiftSummary(),
            'printJobSummary' => $this->adminDashboardService->getPrintJobSummary(),
            'recentSales' => $this->adminDashboardService->getRecentSales(),
            'activeShifts' => $this->adminDashboardService->getActiveShifts(),
            'topProducts' => $this->adminDashboardService->getTopProductsToday(),
        ]);
    }
}