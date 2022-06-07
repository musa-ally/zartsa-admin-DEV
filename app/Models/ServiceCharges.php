<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCharges extends Model
{
    use HasFactory;

    public function serviceDetails()
    {
        return $this->hasOne(Service::class,'id','service_id');
    }
}
