<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\FAQ;
use App\Models\Level;
use App\Models\School;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Banner;
use App\Http\Traits\StatusTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CustomerAddress;

class HomeController extends Controller
{ 
    use StatusTrait;

    protected $state;
    protected $banner;
    protected $customer;
    protected $customerAddress;

    public function __construct(
        State $state, Banner $banner, Customer $customer, CustomerAddress $customerAddress
    ) {
        $this->state = $state;
        $this->banner = $banner;
        $this->customer = $customer;
        $this->customerAddress = $customerAddress;
    }

    //
    public function faqs(Request $request){
        return response()->json(['data'=>FAQ::orderBy('question')->get()]);
    }
    //
    public function batches(Request $request){
        return response()->json(['data'=>Batch::all()]);
    }
    //
    public function semesters(Request $request){
        return response()->json(['data'=>Semester::orderBy('name')->get()]);
    }

    //
    public function levels(Request $request){
        return response()->json(['data'=>Level::all()]);
    }

        
    public function school(Request $request) 
    {
        $school = School::first();
        return response()->json(['data'=> $school]);
    }
        
        
    public function current_accademic_year(Request $request) 
    {
        return response()->json(['data'=> Batch::find(Helpers::instance()->getCurrentAccademicYear())]);
    }




    

    /** 
     * Relation List 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function relation(Request $request) 
    { 
        try 
           {
                $relationArray = [];
                $relations = ['Father','Mother','Husband','Wife'];
                foreach($relations as $key => $relation) 
                {
                    $data['name']     = (string)$relation ?? '';
                    # push in array
                    array_push($relationArray, $data);
                }
                
                # return response
                return response()->json([
                                            'code'      => (string)$this->successStatus, 
                                            'message'   => 'Relation List',
                                            'data'      =>  $relationArray
                                        ]);
                        
            } catch (\Exception $e) {
            # return response
            return response()->json([
                                        'code'      => (string)$this->failedStatus, 
                                        'message'   => 'Something Went Worng',
                                        'data'      =>  []
                                   ]);
            }
    }



    /** 
     * State List 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function state(Request $request) 
    { 
        try 
           {
                $stateArray = [];
                DB::beginTransaction();
                    # get state
                    $states =  $this->state->where('country_id',101)->get();
                DB::commit();      
                        # return response if states get
                        if($states->count() != 0) 
                        {
                            foreach($states as $key => $state) 
                            {
                                $data['id']       = (string)$state->id ?? '';
                                $data['name']     = (string)$state->name ?? '';
                                # push in array
                                array_push($stateArray, $data);
                            }
                            # return response
                            return response()->json([
                                                        'code'      => (string)$this->successStatus, 
                                                        'message'   => 'State List',
                                                        'data'      =>  $stateArray
                                                    ]);
                        }else{
                            # return response
                            return response()->json([
                                                        'code'      => (string)$this->failedStatus, 
                                                        'message'   => 'State Not Found!!',
                                                        'data'      =>  []
                                                    ]); 
                        }
            } catch (\Exception $e) {
            # return response
            return response()->json([
                                        'code'      => (string)$this->failedStatus, 
                                        'message'   => 'Something Went Worng',
                                        'data'      =>  []
                                   ]);
            }
    }




  /** 
     * Banner List 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function banner(Request $request) 
    { 
        try 
           {
                $bannerArray = [];
                DB::beginTransaction();
                    # get banner
                    $banners =  $this->banner->get();
                DB::commit();      
                        # return response if banners get
                        if($banners->isNotEmpty()) 
                        {
                            foreach($banners as $key => $banner) 
                            {
                                $data['id']       = (string)$banner->id ?? '';
                                $data['image']    = (string)$banner->image ?? '';
                                # push in array
                                array_push($bannerArray, $data);
                            }
                            # return response
                            return response()->json([
                                                        'code'      => (string)$this->successStatus, 
                                                        'message'   => 'Banner List',
                                                        'data'      =>  $bannerArray
                                                    ]);
                        }else{
                            # return response
                            return response()->json([
                                                        'code'      => (string)$this->failedStatus, 
                                                        'message'   => 'Banner Not Found!!',
                                                        'data'      =>  []
                                                    ]); 
                        }
            } catch (\Exception $e) {
            # return response
            return response()->json([
                                        'code'      => (string)$this->failedStatus, 
                                        'message'   => 'Something Went Worng',
                                        'data'      =>  []
                                   ]);
            }
    }


}
