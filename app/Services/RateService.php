<?php

namespace App\Services;

use App\Models\Rate;
use App\Models\Cashbox;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Services\CurrencyService;

class RateService
{
    /**
     * Add rates bulk store
     * where creates rate (from_currency_id=incoming_currency_id, to_currency_id=basic_currency_id)
     * and matching pair (with the same cashbox and to_currency_id=incoming_currency_id, from_currency_id=basic_currency_id)
     * collected by incoming filters of currencies
     *
     * @param array $fields
     * @return Collection
     */
    public function bulkStore(array $fields): Collection
    {
        $basicCurrency = (new CurrencyService())->getOrSetBasicCurrency();
        $rates = new Collection();
        foreach ($fields['currencies'] as $currencyId) {
            if ($currencyId == $basicCurrency->id) {
                continue;
            }
            $rate = Rate::firstOrCreate(
                [
                    'from_currency_id' => $currencyId,
                    'to_currency_id' => $basicCurrency->id,
                    'cashbox_id' => $fields['cashbox_id'],
                ],
                ['amount' => 0]
            );
            $rates->push($rate);

            $rate = Rate::firstOrCreate(
                [
                    'to_currency_id' => $currencyId,
                    'from_currency_id' => $basicCurrency->id,
                    'cashbox_id' => $fields['cashbox_id']
                ],
                ['amount' => 0]
            );
            $rates->push($rate);
        }
        return $rates;
    }

    /**
     * Add rates bulk update
     * by incoming filters of cashboxes OR branches OR cities OR regions OR countries
     *
     * @param array $fields
     * @return int
     */
    public function bulkUpdate(array $fields): int
    {
        $updateValues = $this->getUpdateValues($fields);

        return Rate::upsert(
            $updateValues,
            ['from_currency_id', 'to_currency_id', 'cashbox_id'],
            ['amount']
        );
    }

    /**
     * Get array of values for bulk update
     * by incoming filters of cashboxes OR branches OR cities OR regions OR countries
     *
     * @param array $fields
     * @return array
     */
    public function getUpdateValues(array $fields): array
    {
        $query = Cashbox::select('cashboxes.*');

        if (!empty($fields['branches']) || !empty($fields['cities']) || !empty($fields['regions']) || !empty($fields['countries'])) {
            $query->leftJoin('branches', function ($join) {
                $join->on('branches.id', '=', 'cashboxes.branch_id');
            });
        }
        if (!empty($fields['cities']) || !empty($fields['regions']) || !empty($fields['countries'])) {
            $query->leftJoin('cities', function ($join) {
                $join->on('cities.id', '=', 'branches.city_id');
            });
        }
        if (!empty($fields['regions']) || !empty($fields['countries'])) {
            $query->leftJoin('regions', function ($join) {
                $join->on('regions.id', '=', 'cities.region_id');
            });
        }
        if (!empty($fields['countries'])) {
            $query->leftJoin('countries', function ($join) {
                $join->on('countries.id', '=', 'regions.country_id');
            });
        }

        if (!empty($fields['cashboxes'])) {
            $query->orWhereIn('cashboxes.id', $fields['cashboxes']);
        }
        if (!empty($fields['branches'])) {
            $query->orWhereIn('branches.id', $fields['branches']);
        }
        if (!empty($fields['cities'])) {
            $query->orWhereIn('cities.id', $fields['cities']);
        }
        if (!empty($fields['regions'])) {
            $query->orWhereIn('regions.id', $fields['regions']);
        }
        if (!empty($fields['countries'])) {
            $query->orWhereIn('countries.id', $fields['countries']);
        }

        $updateValues = [];
        $uniqueValues = [];
        foreach ($query->get() as $cashbox) {
            if (!empty($uniqueValues[$cashbox->id])){
                if (
                    $uniqueValues[$cashbox->id]['from_currency_id'] == $fields['from_currency_id']
                    && $uniqueValues[$cashbox->id]['to_currency_id'] == $fields['to_currency_id']
                ) {
                    continue;
                }
            }
            $updateValues[] =
                [
                    'id' => Str::uuid()->toString(),
                    'from_currency_id' => $fields['from_currency_id'],
                    'to_currency_id' => $fields['to_currency_id'],
                    'cashbox_id' => $cashbox->id,
                    'amount' => $fields['amount'],
                ];
            if (!Rate::where('to_currency_id', $fields['from_currency_id'])
                ->where('from_currency_id', $fields['to_currency_id'])
                ->where('cashbox_id', $cashbox->id)
                ->first()) {
                $updateValues[] =
                    [
                        'id' => Str::uuid()->toString(),
                        'from_currency_id' => $fields['to_currency_id'],
                        'to_currency_id' => $fields['from_currency_id'],
                        'cashbox_id' => $cashbox->id,
                        'amount' => 0,
                    ];
            }
            $uniqueValues[$cashbox->id] = [
                'from_currency_id' => $fields['from_currency_id'],
                'to_currency_id' => $fields['to_currency_id'],
            ];
        }
        return $updateValues;
    }

    /**
     * Get spread (difference between sell currency rate and buy currency rate)
     *
     * @param array $fields
     * @return int|float
     */
    public function getSpread(array $fields): int|float
    {
        $spread = 0;
        if (!empty($fields['from_currency_id'])
            && !empty($fields['to_currency_id'])
            && !empty($fields['cashbox_id'])) {
            $currencyRate1 = Rate::where('from_currency_id', $fields['from_currency_id'])
                ->where('to_currency_id', $fields['to_currency_id'])
                ->where('cashbox_id', $fields['cashbox_id'])->first();
            $currencyRate2 = Rate::where('from_currency_id', $fields['to_currency_id'])
                ->where('to_currency_id', $fields['from_currency_id'])
                ->where('cashbox_id', $fields['cashbox_id'])->first();
            if (!empty($currencyRate1->amount) && !empty($currencyRate2->amount)) {
                $spread = abs($currencyRate1->amount - $currencyRate2->amount);
            }
        }
        return $spread;
    }
}
