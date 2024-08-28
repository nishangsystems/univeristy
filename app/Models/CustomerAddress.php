<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{

     /**
     * The database table used by the model.
     *
     * @var string
     */
	protected $table 	= 	'customer_addresses';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable 	= 	[
                                    'customer_id',
                                    'state_id',
                                    'district_id',
                                    'tehsil_id',
                                    'area',
                                    'parliamentary_id',
                                    'assembly_id',
							    	'town_village_id',
                                    'panchayat_ward_id',
                                    'block_id',
                                    'thana_id',
                                    'post_office_id',
                                    'locality',
                                    'house_no',
                                    'land_mark',
                                    'pincode',
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
     * Model has One Customer 
     * 
     * @return relation
     */
    public function customerInfo()
    {
        return $this->hasOne('App\Models\Customer','id', 'customer_id');
    }

    

    /**
     * Model has One State 
     * 
     * @return relation
     */
    public function stateInfo()
    {
        return $this->hasOne('App\Models\State','id', 'state_id');
    }


     /**
     * Model has One District 
     * 
     * @return relation
     */
    public function districtInfo()
    {
        return $this->hasOne('App\Models\District','id', 'district_id');
    }

    /**
     * Model has One Tehsil 
     * 
     * @return relation
     */
    public function tehsilInfo()
    {
        return $this->hasOne('App\Models\Tehsil','id', 'tehsil_id');
    }

    /**
     * Model has One Block 
     * 
     * @return relation
     */
    public function blockInfo()
    {
        return $this->hasOne('App\Models\Block','id', 'block_id');
    }


    /**
     * Model has One Parliamentary 
     * 
     * @return relation
     */
    public function parliamentaryInfo()
    {
        return $this->hasOne('App\Models\ParliamentaryConstituency','id', 'parliamentary_id');
    }

    /**
     * Model has One Assembly 
     * 
     * @return relation
     */
    public function assemblyInfo()
    {
        return $this->hasOne('App\Models\AssemblyConstituency','id', 'assembly_id');
    }

     /**
     * Model has One Town Village 
     * 
     * @return relation
     */
    public function townVillageInfo()
    {
        return $this->hasOne('App\Models\TownVillage','id', 'town_village_id');
    }

     /**
     * Model has One Panchayat Ward 
     * 
     * @return relation
     */
    public function panchayatWardInfo()
    {
        return $this->hasOne('App\Models\PanchayatWard','id', 'panchayat_ward_id');
    }


     /**
     * Model has One Thana 
     * 
     * @return relation
     */
    public function thanaInfo()
    {
        return $this->hasOne('App\Models\Thana','id', 'thana_id');
    }


      /**
     * Model has One PostOffice 
     * 
     * @return relation
     */
    public function postOfficeInfo()
    {
        return $this->hasOne('App\Models\PostOffice','id', 'post_office_id');
    }


    
   
}
