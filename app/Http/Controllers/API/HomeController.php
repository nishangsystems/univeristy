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


    
      /**
     * @method to Get Profile
     * 
     * @return 
     */
    public function getProfile(Request $request)
    {
        # Validate request data
        $validator = validator()->make($request->all(), [ 
            'user_id'  => 'required|numeric',
        ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json(['message'=>$validator->errors()->first(),'code' => (string)$this->failedStatus]);            
        }

        try {

        # check user already Exist on that id
        $user = $this->customer
                     ->where('id', $request->get('user_id'))
                     ->where('is_delete', 0)
                     ->get()
                     ->last();
      
        # return response if user already exist on requested user id
        if($user != '') 
        {
            # Set the Data
            $data = [
                        'user_id'          => (string)$user->id,
                        'is_profile_complete' => (string)$user->is_profile_complete,
                        'user_unique_id'   => (string)$user->customer_unique_id,
                        'api_token'        =>  (string)$user->api_token ?? '',
                        'voter_id'         => (string)$user->voter_id,
                        'aadhaar_card'     => (string)$user->aadhaar_card,
                        'name'             => (string)$user->name,
                        'gender'           => (string)$user->gender,
                        'dob'              => (string)$user->dob,
                        'guardian_name'    => (string)$user->guardian_name,
                        'relation'         => (string)$user->relation,
                        'mobile'           => (string)$user->mobile,
                        'email'            => (string)$user->email,
                        'state_id'         => $user->addressInfo ? (string)$user->addressInfo->state_id : '',
                        'state_name'       => $user->addressInfo ? ($user->addressInfo->state_id ? $user->addressInfo->stateInfo->name : '' ) : '',
                        'district_id'      => $user->addressInfo ? (string)$user->addressInfo->district_id : '',
                        'district_name'    => $user->addressInfo ? ($user->addressInfo->district_id ? ($user->addressInfo->districtInfo ? $user->addressInfo->districtInfo->name : '') : '') : '',
                        'tehsil_id'        => $user->addressInfo ? (string)$user->addressInfo->tehsil_id : '',
                        'tehsil_name'    => $user->addressInfo ? ($user->addressInfo->tehsil_id ? ($user->addressInfo->tehsilInfo ? $user->addressInfo->tehsilInfo->name : '') : '') : '',
                        
                        'area'             => $user->addressInfo ? (string)$user->addressInfo->area : '',
                        'parliamentary_id' => $user->addressInfo ? (string)$user->addressInfo->parliamentary_id : '',
                        'parliamentary_name'    => $user->addressInfo ? ($user->addressInfo->parliamentary_id ? ($user->addressInfo->parliamentaryInfo ? $user->addressInfo->parliamentaryInfo->name : '') : '') : '',
                        'assembly_id'      => $user->addressInfo ? (string)$user->addressInfo->assembly_id : '',
                        'assembly_name'    => $user->addressInfo ? ($user->addressInfo->assembly_id ? ($user->addressInfo->assemblyInfo ? $user->addressInfo->assemblyInfo->name : '') : '') : '',
                        
                        'town_village_id'      => $user->addressInfo ? (string)$user->addressInfo->town_village_id : '',
                        'town_village_name'    => $user->addressInfo ? ($user->addressInfo->town_village_id ? ($user->addressInfo->townVillageInfo ? $user->addressInfo->townVillageInfo->name : '') : '') : '',


                        'panchayat_ward_id'         => $user->addressInfo ? (string)$user->addressInfo->panchayat_ward_id : '',
                        'panchayat_ward_name'    => $user->addressInfo ? ($user->addressInfo->panchayat_ward_id ? ($user->addressInfo->panchayatWardInfo ? $user->addressInfo->panchayatWardInfo->name : '') : '') : '',

                        'block_id'         => $user->addressInfo ? (string)$user->addressInfo->block_id : '',
                        'block_name'    => $user->addressInfo ? ($user->addressInfo->block_id ? ($user->addressInfo->blockInfo ? $user->addressInfo->blockInfo->name : '') : '') : '',

                        'thana_id'         => $user->addressInfo ? (string)$user->addressInfo->thana_id : '',
                        'thana_name'    => $user->addressInfo ? ($user->addressInfo->thana_id ? ($user->addressInfo->thanaInfo ? $user->addressInfo->thanaInfo->name : '') : '') : '',

                        'post_office_id'      => $user->addressInfo ? (string)$user->addressInfo->post_office_id : '',
                        'post_office_name'    => $user->addressInfo ? ($user->addressInfo->post_office_id ? ($user->addressInfo->postOfficeInfo ? $user->addressInfo->postOfficeInfo->name : '') : '') : '',


                        'locality'         => $user->addressInfo ? (string)$user->addressInfo->locality : '',
                        'house_no'         => $user->addressInfo ? (string)$user->addressInfo->house_no : '',
                        'land_mark'        => $user->addressInfo ? (string)$user->addressInfo->land_mark : '',
                        'pincode'          => $user->addressInfo ? (string)$user->addressInfo->pincode : '',
                    ];

            # return response
            return response()->json([
                'code'      => (string)$this->successStatus, 
                'message'   => 'Profile Details.',
                'data'      => $data
             ]);
              
        } else {
            # return response
            return response()->json([
                'code'      => (string)$this->failedStatus, 
                'message'   => 'User not Found on User Id.',
                'data'      => []
             ]); 
        } 


        } catch (\Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }

}
