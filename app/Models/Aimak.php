<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Aimak extends Model
{
    //
    protected $fillable = ['name','permission'];

    public function  bolges()
    {
        return $this->hasMany(Bolge::class,'aimak_id');
    }

    public function scopeVisibleToUser(Builder $query): Builder
    {
        $user = filament()->auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('Супер админ')) {
            return $query;
        }

        // Включает личные разрешения и разрешения через роли.
        $permissions = $user->getAllPermissions()->pluck('name');

        return $query->whereIn(
            $query->getModel()->qualifyColumn('permission'),
            $permissions,
        );
    }
}
