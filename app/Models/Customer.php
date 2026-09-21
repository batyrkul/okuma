<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    //
    protected $fillable = ['name', 'aimak_id','bolge_id'];

    public function bolge(){
        return $this->belongsTo(Bolge::class);
    }

    public function aimak(){
        return $this->belongsTo(Aimak::class);
    }

    public function scopeVisibleToUser(Builder $query): Builder
    {
        return $query
            ->whereHas(
                'aimak',
                fn (Builder $query) => $query->visibleToUser(),
            )
            ->whereHas(
                'bolge.aimak',
                fn (Builder $query) => $query->visibleToUser(),
            );
    }

}
