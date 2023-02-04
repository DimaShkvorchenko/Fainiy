<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Traits\{Uuids, OrderByDateDesc};

/**
 * App\Models\Currency
 *
 * @property string $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $iso_code
 * @property string $name
 * @method static where(string $string, $NULL)
 */

class Currency extends Model
{
    use HasFactory, SoftDeletes, Uuids, OrderByDateDesc;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
