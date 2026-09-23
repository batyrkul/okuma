<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;


class User extends Authenticatable implements \Filament\Models\Contracts\FilamentUser
{

    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;


    protected $fillable = [
        'name',
        'email',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
    protected static function booted()
    {
        static::saved(function ($user) {
            Cache::flush();
        });

        static::deleted(function ($user) {
            Cache::flush();
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'comid');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(Obiect::class, 'object_id');
    }

    protected $casts = [
        'category' => \App\Enums\EmCategory::class,
    ];


    public function routeNotificationForTelegram()
    {
        return $this->telegram_chat_id;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

}
