<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schadule extends Model
{
    //
    protected $fillable = ['customer_id','date','total'];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }
}
