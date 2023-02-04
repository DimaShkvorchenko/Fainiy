<?php

namespace App\Services\Commission;

use App\Models\{Setting, User};

class IncomeCommission implements CommissionInterface
{
    /**
     * Get or set up income commission
     *
     * @return Setting
     */
    public function getOrSetCommissionSetting(): Setting
    {
        return Setting::firstOrCreate(
            ['code' => 'income_commission'],
            ['value' => '2']
        );
    }

    /**
     * Get or set up client income commission
     *
     * @param string $clientId
     * @return int|float
     */
    public function getOrSetClientIncomeCommission(string $clientId): int|float
    {
        $incomeCommission = 0;
        if ($client = User::find($clientId)) {
            $clientIncomeCommissionExists = false;
            if (!empty($client->other_data)) {
                $otherData = json_decode($client->other_data);
                if (isset($otherData->income_commission)) {
                    $clientIncomeCommissionExists = true;
                    $incomeCommission = (float)$otherData->income_commission;
                }
            }
            if (!$clientIncomeCommissionExists){
                $otherData = [];
                $incomeCommission = $otherData['income_commission']
                    = (float)$this->getOrSetCommissionSetting()->value;
                $client->other_data = json_encode($otherData);
                $client->save();
            }
        } else {
            $incomeCommission = (float)$this->getOrSetCommissionSetting()->value;
        }
        return $incomeCommission;
    }

    /**
     * Get amount without commission
     *
     * @param  array  $fields
     * @return int|float
     */
    public function getAmountWithoutCommission(array $fields): int|float
    {
        if (!empty($fields['amount'])) {
            return (!empty($fields['commission']))? $fields['amount'] - round(($fields['commission'] * $fields['amount'] / 100), 2) : $fields['amount'];
        }
        return 0;
    }
}
