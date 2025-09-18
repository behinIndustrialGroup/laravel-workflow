<?php

namespace App\Models;

use BehinInit\App\Http\Controllers\AccessController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ACTIVE_SCOPE = 'active';

    protected static function booted(): void
    {
        static::addGlobalScope(self::ACTIVE_SCOPE, function (Builder $builder) {
            $builder->where('is_disabled', false);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_disabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_disabled' => 'boolean',
        ];
    }

    public function scopeWithDisabled(Builder $query): Builder
    {
        return $query->withoutGlobalScope(self::ACTIVE_SCOPE);
    }

    public function scopeOnlyDisabled(Builder $query): Builder
    {
        return $query->withDisabled()->where('is_disabled', true);
    }
    function access($method_name) {
        return (new AccessController($method_name))->check();
    }
}
