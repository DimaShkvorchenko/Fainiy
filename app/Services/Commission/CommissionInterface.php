<?php

namespace App\Services\Commission;

use App\Models\Setting;

interface CommissionInterface
{
    /**
     * Get or set up commission
     *
     * @return Setting
     */
    public function getOrSetCommissionSetting(): Setting;
}
