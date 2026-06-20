<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\Admin\IncomeAnalysisService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeuanganDashboardController extends Controller
{
    public function __construct(
        protected IncomeAnalysisService $adminIncomeAnalysisService
    ) {}

    public function index(Request $request): View
    {
        return view('finance.dashboard', [
            'filters' => $this->adminIncomeAnalysisService->getFilterState($request),
            'scopeLabel' => $this->adminIncomeAnalysisService->getScopeLabel($request),

            'summary' => $this->adminIncomeAnalysisService->getSummary($request),
            'incomeChart' => $this->adminIncomeAnalysisService->getChartData($request),
            'topProducts' => $this->adminIncomeAnalysisService->getTopProducts($request),
            'cashierPerformance' => $this->adminIncomeAnalysisService->getCashierPerformance($request),
            'expenseBreakdown' => $this->adminIncomeAnalysisService->getExpenseBreakdown($request),

            'cashiers' => $this->adminIncomeAnalysisService->getCashiers(),
            'chartViews' => $this->adminIncomeAnalysisService->chartViews(),
            'chartMetrics' => $this->adminIncomeAnalysisService->chartMetrics(),
            'months' => $this->adminIncomeAnalysisService->months(),
            'years' => $this->adminIncomeAnalysisService->years(),

            // Tambahan khusus agar form filter bisa pakai route finance
            'analysisRoute' => 'finance.dashboard',
        ]);
    }
}