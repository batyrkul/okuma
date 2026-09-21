<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bolge extends Model
{
    //
    protected $fillable = ['name', 'aimak_id'];

    public function aimak()
    {
        return $this->belongsTo(Aimak::class);
    }

}
