<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{

     /**
     * The database table used by the model.
     *
     * @var string
     */
	protected $table 	= 	'banners';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable 	= 	[
                                    'image',
							    	'title',
					      		];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

   
}
