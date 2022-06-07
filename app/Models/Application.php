<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{

	protected $table = 'customer_service_applications';


	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
     'service_code',
	 'external_users_id',
	 'application_status_code',
        'user_type',
        'payer_full_name',
        'payer_phone_number',
        'payer_email_address'

    ];

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];


}
