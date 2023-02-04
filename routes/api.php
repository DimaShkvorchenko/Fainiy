<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    EmailPasswordLoginController,
    TelegramLoginController,
    LogoutController,
    ChangePasswordController
};
use App\Http\Controllers\Admin\Users\{
    AdminController,
    ClientController,
    StaffController,
    ProfileController,
    TelegramController,
    WalletController as UserWalletController
};
use App\Http\Controllers\Admin\{
    CurrencyController,
    RateController,
    ExchangeController,
    ActionController,
    SettingController,
    TaskController,
    TransferController,
    IncomeController,
    WalletController,
    CashboxController,
    PermissionsController,
    RoleController
};
use App\Http\Controllers\Locations\{
    CountryController,
    RegionController,
    CityController,
    BranchController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/**
 * Admin Panel routes
 */
Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminController::class, 'store'])->name('register');
    Route::post('/login', [EmailPasswordLoginController::class, 'login'])->name('login');
    Route::get('/telegram-login', [TelegramLoginController::class, 'login']);

    /**
     * Routes opened only for admins and staff
     */
    Route::middleware(['auth:sanctum', 'scope.admin'])->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
        Route::put('/change-password', [ChangePasswordController::class, 'changePassword'])->name('changePassword');
        Route::get('/user', [AdminController::class, 'index']);

        Route::apiResource('clients', ClientController::class);
        Route::delete('/clients', [ClientController::class, 'bulkDestroy']);
        Route::apiResource('staff', StaffController::class);
        Route::delete('/staff', [StaffController::class, 'bulkDestroy']);
        Route::apiResource('currencies', CurrencyController::class);
        Route::delete('/currencies', [CurrencyController::class, 'bulkDestroy']);
        Route::apiResource('rates', RateController::class);
        Route::delete('/rates', [RateController::class, 'bulkDestroy']);
        Route::apiResource('exchanges', ExchangeController::class);
        Route::apiResource('tasks', TaskController::class);
        Route::delete('/tasks', [TaskController::class, 'bulkDestroy']);
        Route::apiResource('transfers', TransferController::class);
        Route::apiResource('income', IncomeController::class);
        Route::apiResource('settings', SettingController::class);
        Route::apiResource('countries', CountryController::class);
        Route::delete('/countries', [CountryController::class, 'bulkDestroy']);
        Route::apiResource('regions', RegionController::class);
        Route::delete('/regions', [RegionController::class, 'bulkDestroy']);
        Route::apiResource('cities', CityController::class);
        Route::delete('/cities', [CityController::class, 'bulkDestroy']);
        Route::get('/cities-to-rates', [CityController::class, 'citiesToRatesIndex']);
        Route::apiResource('branches', BranchController::class);
        Route::delete('/branches', [BranchController::class, 'bulkDestroy']);
        Route::get('/branches-to-wallets', [BranchController::class, 'branchesToWalletsIndex']);
        Route::apiResource('cashboxes', CashboxController::class);
        Route::delete('/cashboxes', [CashboxController::class, 'bulkDestroy']);
        Route::apiResource('wallet', WalletController::class)->only([
            'index', 'show'
        ]);
        Route::apiResource('actions', ActionController::class)->only([
            'index', 'show'
        ]);
        Route::apiResource('/roles', RoleController::class);
        Route::apiResource('/permissions',PermissionsController::class);
    });
});

Route::middleware(['auth:sanctum', 'scope.admin'])->group(function () {
    Route::apiResource('profile', ProfileController::class)->only([
        'show', 'update'
    ]);
    Route::apiResource('profile/telegram', TelegramController::class)->only([
        'show', 'update'
    ]);
    Route::apiResource('wallet', UserWalletController::class)->only([
        'index', 'show'
    ]);
    Route::post('transfers/{transfer}/issue', [TransferController::class, 'setStatusDone']);
    Route::post('transfers/{transfer}/cancel', [TransferController::class, 'setStatusCancelled']);
});
