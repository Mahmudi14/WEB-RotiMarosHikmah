<?php

use App\Http\Controllers\Admin\CashierController as AdminCashierController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\PosTerminalController as AdminPosTerminalController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromoController as AdminPromoController;
use App\Http\Controllers\Admin\TaxController as AdminTaxController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\IncomeAnalysisController as AdminIncomeAnalysisController;
use App\Http\Controllers\Cashier\CashierDashboardController;
use App\Http\Controllers\Cashier\CashierExpenseController;
use App\Http\Controllers\Cashier\CashierPosController;
use App\Http\Controllers\Cashier\CashierShiftController;
use App\Http\Controllers\Cashier\CashierTransactionController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Finance\KeuanganDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\ActivityLogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\LoginHistoryController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect Berdasarkan Role
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardRedirectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Breeze
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
});

/*
|--------------------------------------------------------------------------
| Kasir Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:kasir,super_admin'])
    ->prefix('cashier')
    ->name('cashier.')
    ->group(function () {
        Route::get('dashboard', [CashierDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('transactions', [CashierTransactionController::class, 'index'])
            ->name('transactions.index');
        Route::get('transactions/{sale}', [CashierTransactionController::class, 'show'])
            ->name('transactions.show');
        Route::post('transactions/{sale}/reprint', [CashierTransactionController::class, 'reprint'])
            ->name('transactions.reprint');

        Route::get('shifts', [CashierShiftController::class, 'index'])
            ->name('shifts.index');
        Route::get('shifts/create', [CashierShiftController::class, 'create'])
            ->name('shifts.create');
        Route::post('shifts', [CashierShiftController::class, 'store'])
            ->name('shifts.store');
        Route::get('shifts/{cashier_shift}', [CashierShiftController::class, 'show'])
            ->name('shifts.show');
        Route::get('shifts/{cashier_shift}/close', [CashierShiftController::class, 'closeForm'])
            ->name('shifts.close-form');
        Route::patch('shifts/{cashier_shift}/close', [CashierShiftController::class, 'closeShift'])
            ->name('shifts.close');

        Route::resource('expenses', CashierExpenseController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::get('pos', [CashierPosController::class, 'index'])->name('pos.index');
        Route::post('pos/sales', [CashierPosController::class, 'store'])->name('pos.sales.store');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('transactions', [AdminTransactionController::class, 'index'])
            ->name('transactions.index');

        Route::get('transactions/{sale}', [AdminTransactionController::class, 'show'])
            ->name('transactions.show');

        Route::get('income-analysis', [AdminIncomeAnalysisController::class, 'index'])
            ->name('income-analysis.index');

        Route::patch('categories/reorder', [AdminCategoryController::class, 'reorder'])
            ->name('categories.reorder');
        Route::patch('categories/{category}/status', [AdminCategoryController::class, 'updateStatus'])
            ->name('categories.update-status');
        Route::resource('categories', AdminCategoryController::class);

        Route::patch('products/{product}/availability', [AdminProductController::class, 'updateAvailability'])
            ->name('products.update-availability');
        Route::patch('products/{product}/status', [AdminProductController::class, 'updateStatus'])
            ->name('products.update-status');
        Route::resource('products', AdminProductController::class);

        Route::patch('promos/{promo}/status', [AdminPromoController::class, 'updateStatus'])
            ->name('promos.update-status');
        Route::resource('promos', AdminPromoController::class);

        Route::patch('taxes/{tax}/status', [AdminTaxController::class, 'updateStatus'])
            ->name('taxes.update-status');
        Route::resource('taxes', AdminTaxController::class);

        Route::patch('pos-terminals/{pos_terminal}/status', [AdminPosTerminalController::class, 'updateStatus'])
            ->name('pos-terminals.update-status');
        Route::patch('pos-terminals/{pos_terminal}/regenerate-token', [AdminPosTerminalController::class, 'regenerateToken'])
            ->name('pos-terminals.regenerate-token');
        Route::resource('pos-terminals', AdminPosTerminalController::class);

        Route::patch('cashiers/{cashier}/status', [AdminCashierController::class, 'updateStatus'])
            ->name('cashiers.update-status');
        Route::patch('cashiers/{cashier}/reset-password', [AdminCashierController::class, 'resetPassword'])
            ->name('cashiers.reset-password');
        Route::resource('cashiers', AdminCashierController::class)
            ->except(['destroy']);
    });

/*
|--------------------------------------------------------------------------
| Keuangan Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:keuangan,super_admin'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/dashboard', [KeuanganDashboardController::class, 'index'])
            ->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])
            ->name('dashboard');
        Route::resource('users', SuperAdminUserController::class)->except(['show']);
    });

require __DIR__ . '/auth.php';