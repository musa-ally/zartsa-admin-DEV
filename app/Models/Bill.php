<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
	
	protected $table = 'customer_application_bills';
	
		
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
     'amount_tzs',
	 'amount_usd',
	 'exchange_rate',
	 'control_number',
	 'expiring_datetime',
	 'customer_service_applications_id', 
	 'customer_service_applications_services_code',
	 'customer_service_applications_external_users_id',
	 'bill_status_id',
        'user_type'
    ];
	
	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
	

}
