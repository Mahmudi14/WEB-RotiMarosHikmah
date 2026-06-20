<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\Admin\TransactionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $adminTransactionService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.transactions.index', [
            'sales' => $this->adminTransactionService->getPaginatedSales($request),
            'summary' => $this->adminTransactionService->getSummary($request),
            'paymentMethods' => $this->adminTransactionService->paymentMethods(),
            'statuses' => $this->adminTransactionService->statuses(),
            'cashiers' => $this->adminTransactionService->getCashiers(),
            'terminals' => $this->adminTransactionService->getTerminals(),
        ]);
    }

    public function show(Sale $sale): View
    {
        return view('admin.transactions.show', [
            'sale' => $this->adminTransactionService->findSale($sale),
        ]);
    }
}