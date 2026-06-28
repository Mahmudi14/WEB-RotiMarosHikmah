<?php

use App\Http\Controllers\Admin\{
    CashierController as AdminCashierController,
    CategoryController as AdminCategoryController,
    DashboardController as AdminDashboardController,
    IncomeAnalysisController as AdminIncomeAnalysisController,
    PosTerminalController as AdminPosTerminalController,
    ProductController as AdminProductController,
    PromoController as AdminPromoController,
    StockController as AdminStockController,
    TaxController as AdminTaxController,
    TransactionController as AdminTransactionController
};
use App\Http\Controllers\Cashier\{
    CashierDashboardController,
    CashierExpenseController,
    CashierPosController,
    CashierShiftController,
    CashierTransactionController
};
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Finance\{
    KeuanganDashboardController,
    StockController as FinanceStockController
};
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\{
    DashboardController as SuperAdminDashboardController,
    UserController as SuperAdminUserController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('welcome');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard Redirect Berdasarkan Role
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile Breeze
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    /*
    |--------------------------------------------------------------------------
    | Role Protected Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('verified')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Kasir Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:kasir')
            ->prefix('cashier')
            ->name('cashier.')
            ->group(function () {
                Route::get('dashboard', [CashierDashboardController::class, 'index'])
                    ->name('dashboard');

                Route::controller(CashierTransactionController::class)
                    ->prefix('transactions')
                    ->name('transactions.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('{sale}', 'show')->name('show');
                        Route::post('{sale}/reprint', 'reprint')->name('reprint');
                    });

                Route::controller(CashierShiftController::class)
                    ->prefix('shifts')
                    ->name('shifts.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('create', 'create')->name('create');
                        Route::post('/', 'store')->name('store');
                        Route::get('{cashier_shift}', 'show')->name('show');
                        Route::get('{cashier_shift}/close', 'closeForm')->name('close-form');
                        Route::patch('{cashier_shift}/close', 'closeShift')->name('close');
                        Route::post('{cashierShift}/reprint-report', 'reprintReport')->name('reprint-report');
                    });

                Route::resource('expenses', CashierExpenseController::class)
                    ->only(['index', 'create', 'store', 'show', 'destroy']);

                Route::controller(CashierPosController::class)
                    ->prefix('pos')
                    ->name('pos.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('sales', 'store')->name('sales.store');
                    });
            });

        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:admin')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::get('dashboard', [AdminDashboardController::class, 'index'])
                    ->name('dashboard');

                Route::controller(AdminTransactionController::class)
                    ->prefix('transactions')
                    ->name('transactions.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('{sale}', 'show')->name('show');
                        Route::patch('{sale}/cancel', 'cancel')->name('cancel');
                    });

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

                Route::controller(AdminStockController::class)
                    ->prefix('stocks')
                    ->name('stocks.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('{product}/movements', 'movements')->name('movements');
                        Route::post('{product}/adjust', 'adjust')->name('adjust');
                    });
            });

        /*
        |--------------------------------------------------------------------------
        | Keuangan Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:keuangan')
            ->prefix('finance')
            ->name('finance.')
            ->group(function () {
                Route::get('dashboard', [KeuanganDashboardController::class, 'index'])
                    ->name('dashboard');

                Route::controller(FinanceStockController::class)
                    ->prefix('stocks')
                    ->name('stocks.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('{product}/movements', 'movements')->name('movements');
                    });
            });

        /*
        |--------------------------------------------------------------------------
        | Super Admin Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super_admin')
            ->prefix('super-admin')
            ->name('super-admin.')
            ->group(function () {
                Route::get('dashboard', [SuperAdminDashboardController::class, 'index'])
                    ->name('dashboard');

                Route::resource('users', SuperAdminUserController::class)
                    ->except(['show']);
            });
    });
});

require __DIR__ . '/auth.php';