<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\{ActionService, RateService, CurrencyService};

abstract class BaseCollectionController extends Controller
{
    public function __construct(
        protected ActionService $actionService,
        protected RateService $rateService,
        protected CurrencyService $currencyService
    )
    {
    }
}
