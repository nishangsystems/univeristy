<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{

     /**
     * The database table used by the model.
     *
     * @var string
     */
	protected $table 	= 	'customers';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable 	= 	[
                                    'customer_unique_id',
                                    'voter_id',
                                    'aadhaar_card',
                                    'name',
							    	'gender',
                                    'dob',
                                    'age',
                                    'guardian_name',
                                    'relation',
                                    'mobile',
                                    'email',
                                    'password',
                                    'show_password',
                                    'otp',
                                    'forgot_otp',
                                    'is_profile_complete',
                                    'status',
                                    'is_delete',
                                    'is_executive',
                                    'api_token',
                                    'device_type',
                                    'device_token',
					      		];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

   /**
     * Model has One Customer Address 
     * 
     * @return relation
     */
    public function addressInfo()
    {
        return $this->hasOne('App\Models\CustomerAddress','customer_id', 'id');
    }

    
    
   
}
