<?php
namespace App\Http\Traits;

use App\Brand;

trait StatusTrait {

    # set success Status
    protected $successStatus = 200;

    # set failed Status
    protected $failedStatus = 401;

    # Set App Name
    protected $appName = 'Lots';

    # Set Miles (6.21 equals 10Km)
    protected $miles = 6.21;
}