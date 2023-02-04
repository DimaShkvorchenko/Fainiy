<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Traits\{Uuids, OrderByDateDesc};

/**
 * App\Models\Wallet
 *
 * @property string $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $client_id
 * @property string $currency_id
 * @property double $amount
 */

class Wallet extends Model
{
    use HasFactory, SoftDeletes, Uuids, OrderByDateDesc;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the currency that owns the wallet.
     *
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the client that owns the wallet.
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the cashbox that owns the wallet.
     *
     * @return BelongsTo
     */
    public function cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class);
    }
}
