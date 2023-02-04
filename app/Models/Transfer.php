<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Traits\{Uuids, OrderByDateDesc};

/**
 * App\Models\Transfer
 *
 * @property string $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $access_code
 * @property double $amount
 * @property string $commission
 * @property string $user_id
 * @property string $client_id
 * @property string $from_branch_id
 * @property string $to_branch_id
 * @property string $currency_id
 * @property string $description
 * @property string $status
 * @property Carbon|null $time_of_issue
 */

class Transfer extends Model
{
    use HasFactory, SoftDeletes, Uuids, OrderByDateDesc;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the currency that owns the transfer.
     *
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the user that owns the transfer.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client that owns the transfer.
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the from_cashbox that owns the transfer.
     *
     * @return BelongsTo
     */
    public function from_cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class, 'from_cashbox_id');
    }

    /**
     * Get the to_cashbox that owns the transfer.
     *
     * @return BelongsTo
     */
    public function to_cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class, 'to_cashbox_id');
    }
}
