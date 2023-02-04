<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use App\Traits\{Uuids, OrderByDateDesc};
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $phone
 * @property int $account_type
 * @property int $is_admin
 * @property int $is_client
 * @property int $is_staff
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Task[] $tasks
 * @property-read int|null $tasks_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User admin()
 * @method static Builder|User adminOwner($id)
 * @method static Builder|User client()
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User staff()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsAdmin($value)
 * @method static Builder|User whereIsClient($value)
 * @method static Builder|User whereIsStaff($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    public const ADMIN_TYPE = 1;
    public const STAFF_TYPE = 2;
    public const CLIENT_TYPE = 3;
    public const TYPES = [
        self::ADMIN_TYPE => 'admin',
        self::STAFF_TYPE => 'staff',
        self::CLIENT_TYPE => 'client',
    ];

    use HasApiTokens, SoftDeletes, HasFactory, Notifiable, Uuids, OrderByDateDesc, HasRoles;

    protected $guarded = [];

    /**
     * Параметр который скрывает поля от выдачи
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('account_type', 1);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('account_type', 2);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeClient(Builder $query): Builder
    {
        return $query->where('account_type', 3);
    }

    /**
     * @param Builder $query
     * @param $id
     * @return Builder
     */
    public function scopeAdminOwner(Builder $query, $id): Builder
    {
        return $query->where('admin_id', $id);
    }

    /**
     * @return BelongsToMany
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'user_task');
    }
}
