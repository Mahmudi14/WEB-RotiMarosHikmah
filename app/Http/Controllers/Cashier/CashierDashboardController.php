<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Services\Cashier\CashierDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierDashboardController extends Controller
{
    public function __construct(
        protected CashierDashboardService $cashierDashboardService
    ) {}

    public function index(Request $request): View
    {
        $cashier = $request->user();

        $activeShift = $this->cashierDashboardService->getActiveShift($cashier);

        return view('cashier.dashboard', [
            'activeShift' => $activeShift,
            'shiftSummary' => $this->cashierDashboardService->getShiftSummary($activeShift),
            'todaySummary' => $this->cashierDashboardService->getTodaySummary($cashier),
            'recentSales' => $this->cashierDashboardService->getRecentSales($cashier),
            'topProducts' => $this->cashierDashboardService->getTopProductsToday($cashier),
            'printJobSummary' => $this->cashierDashboardService->getPrintJobSummary($activeShift),
        ]);
    }
}