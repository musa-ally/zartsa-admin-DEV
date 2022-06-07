<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
	
	protected $table = 'exchange_rate';
	
		
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
     'spot_buying','mean','spot_selling','exchange_date'
    ];
	
	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

}
