<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargoSelection extends Model
{
	
	protected $table = 'customer_cargo_selections';
	
		
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
     'id',
     'cargo_id',
	 'cargo_cargo_types_id',
	 'cargo_bill_of_ladings_id',
	 'cargo_bill_of_ladings_voyages_id',
	 'cargo_bill_of_ladings_voyages_zpc_ports_id',
	 'customer_service_applications_id',
	 'cargo_bill_of_ladings_voyages_vessels_id',
	 'customer_service_applications_services_code',
	 'customer_service_applications_external_users_id',
	 'service_bill_formulars_id'
    ];
	
	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
	

}
