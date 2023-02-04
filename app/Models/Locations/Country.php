<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Traits\{Uuids, OrderByDateDesc};

/**
 * App\Models\Locations\Country
 *
 * @property string $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $iso_code
 * @property string $name
 */

class Country extends Model
{
    use HasFactory, SoftDeletes, Uuids, OrderByDateDesc;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the regions for the Country.
     *
     * @return hasMany
     */
    public function regions(): hasMany
    {
        return $this->hasMany(Region::class);
    }
}
