<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

ini_set('memory_limit', '64M');

use App\Models\User;
use App\Services\RegisterService;

uses(Tests\TestCase::class)->in('Feature');

const CLIENTS_URL = 'api/admin/clients';
const STAFF_URL = 'api/admin/staff';
const PROFILE_URL = 'api/profile';
const TELEGRAM_URL = 'api/profile/telegram';
const CREATE_TOKEN_URL = 'api/admin/login';
const LOGIN_BY_TELEGRAM_URL = 'api/admin/telegram-login';
const REGISTER_URL = 'api/admin/register';
const RATE_URL = 'api/admin/rates';
const CURRENCY_URL = 'api/admin/currencies';
const EXCHANGE_URL = 'api/admin/exchanges';
const TRANSFER_URL = 'api/admin/transfers';
const TRANSFER_STATUS_DONE_URL = 'api/transfers/ID/issue';
const TRANSFER_STATUS_CANCELLED_URL = 'api/transfers/ID/cancel';
const INCOME_URL = 'api/admin/income';
const WALLET_URL = 'api/admin/wallet';
const ACTION_URL = 'api/admin/actions';
const SETTING_URL = 'api/admin/settings';
const COUNTRY_URL = 'api/admin/countries';
const REGION_URL = 'api/admin/regions';
const CITY_URL = 'api/admin/cities';
const BRANCH_URL = 'api/admin/branches';
const CASHBOX_URL = 'api/admin/cashboxes';
const TASK_URL = 'api/admin/tasks';

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createAdmin()
{
    try {
        return User::where('email', ADMIN_PAYLOAD['email'])->firstOrFail()->id;
    } catch (\Throwable $e) {
        $service = new RegisterService();
        return $service->createAdmin(ADMIN_PAYLOAD)->id;
    }
}

function getClientId()
{
    try {
        return User::where('email', CLIENT_PAYLOAD['email'])->firstOrFail()->id;
    } catch (\Throwable $e) {
        $service = new RegisterService();
        return $service->createClient(CLIENT_PAYLOAD)->id;
    }
}

function getStaffId()
{
    try {
        return User::where('email', STAFF_PAYLOAD['email'])->firstOrFail()->id;
    } catch (\Throwable $e) {
        $service = new RegisterService();
        return $service->createStaff(STAFF_PAYLOAD)->id;
    }
}
