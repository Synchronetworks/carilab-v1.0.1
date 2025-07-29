<?php

namespace Modules\Vendor\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\Document\Models\Document;
use Modules\Vendor\Models\VendorDocument;
use Modules\Subscriptions\Models\Plan;
use Modules\Tax\Models\Tax;
use Modules\Lab\Models\Lab;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use Modules\Lab\Models\LabTaxMapping;
use Modules\Lab\Models\LabLocationMapping;
use Modules\World\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Modules\Lab\Http\Requests\LabRequest;
use App\Trait\ModuleTrait;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
use Illuminate\Support\Facades\Auth;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Constant\Models\Constant;
use Modules\Category\Models\Category;
use Modules\User\Http\Requests\UserRequest;
use Modules\PackageManagement\Models\PackageManagement;
use Illuminate\Support\Facades\Validator;
use Modules\CatlogManagement\Http\Requests\CatlogManagementRequest;
use Illuminate\Support\Facades\Cache;
use App\Models\UserCommissionMapping;
use Modules\Commision\Models\Commision;
use Modules\Lab\Models\LabSession;
class VendorRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vendor::backend.VendorRegistration.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)

    { 
         $step = $request->query('step', 1); // Default to step 1 if not provided
      
        return view('vendor::backend.VendorRegistration.index', compact('step'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('vendor::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('vendor::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function showWizardStep(Request $request, $step)
    {
        
        switch ($step) {
            case 1:
                return view('vendor::backend.VendorRegistration.vendor_basic_details');
            case 2:
                $vendor_id= $request->vendor_id;
              $document=  Document::where('user_type','vendor')->where('status',1)->get();
                return view('vendor::backend.VendorRegistration.upload_document',compact('document','vendor_id'));
            case 3:
                $plans = Plan::with('planLimitation')->get();
                 $vendor_id= $request->vendor_id;
                return view('vendor::backend.VendorRegistration.choose_package', compact('plans','vendor_id'));
            case 4:
                $vendor_id= $request->vendor_id;
                $countries = Country::pluck('name', 'id');
                $taxes = Tax::get();
                $locations = collect([]);
                $paymentGateways = Setting::where('datatype', 'payment_gateways')->where('val', 1)->get();
                $assets = ['textarea']; 
            
                return view('vendor::backend.VendorRegistration.lab_details', compact('vendor_id', 'countries', 'taxes', 'paymentGateways', 'locations','assets'));
            case 5:
                $labs = Lab::all(); 
                $selectedLabId = $request->lab_id;
                $vendor_id= $request->vendor_id ?? auth()->id();
                $test_types = Constant::where('type','test_type')->get();
                $categories = Category::all();
                $equipments = Constant::where('type','equipment')->get();
                return view('vendor::backend.VendorRegistration.select_test',compact('labs','vendor_id','categories', 'selectedLabId','test_types','equipments'));
                case 6:
                    $data['vendor_id'] = $request->vendor_id ?? auth()->id(); 
                    if($data['vendor_id'] != null){
                        $vendor = User::where('id',$data['vendor_id'])->first();
                        $vendor->update(['status' => 1]);
                    }
                    return view('vendor::backend.VendorRegistration.success');
            default:
                return response()->json(['error' => __('messages.invalid_step')], 400);
        }
    }


    public function storeStepData(Request $request, $step)
    {
        $validatedData = [];


        if ($step == 1) {
            $rules = (new UserRequest())->rules(); 

            $validator = Validator::make($request->all(), $rules);
        
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $data = $request->all();
            
                if (isset($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                }
               
                $data['user_type'] = 'vendor'; 
                $data['status'] = 1; 
                $vendor = User::create($data);
                $vendor->assignRole('vendor');
                Cache::put('vendor_id', $vendor->id);
                session(['vendor_id' => $vendor->id]);
                if(setting('vendor_commission_type') =='per_vendor'){
                    
                    if (isset($data['commission_type'], $data['commission'])) {
                        $commissionMappingData = [
                            'user_id'         => $vendor->id,
                            'commission_type' => $data['commission_type'],
                            'commission'      => $data['commission'],
                            'created_by'      => auth()->id(),
                            'updated_by'      => auth()->id(),
                        ];
                        UserCommissionMapping::create($commissionMappingData);
                    }
                }
             
                if ($request->hasFile('profile_image')) {
                    storeMediaFile($vendor, $request->file('profile_image'), 'profile_image');
                }
                return response()->json(['success' => true,'vendor_id'=>$vendor->id]);
        }
        
        
        if ($step == 2) {
            $data = $request->all();
            
            $vendorId = $data['vendor_id'];
      
            if (!empty($request->document_ids)) {
                foreach ($request->document_ids as $documentId) {
                    if ($request->hasFile("documents.$documentId")) {
                        $documentFile = $request->file("documents.$documentId");
                        $result = VendorDocument::updateOrCreate(
                            [
                                'vendor_id'   => $vendorId,
                                'document_id' => $documentId,
                            ],
                            [
                                'is_verified' => $data['is_verified'] ?? 0,
                            ]
                        );
                        if ($result->is_verified == 1) {
                            User::where('id', $vendorId)->update(['is_verify' => 1]);
                        }

                        storeMediaFile($result, $documentFile, 'vendor_document');
                    }
                }
            }
            $message = __('messages.update_form', ['form' => __('messages.vendordocument')]);
            return response()->json(['success' => true, 'message' => $message, 'vendor_id'=>$vendorId]);
        }
        

       
        if ($step == 3) {
            $validatedData = $request->validate([
                'package' => 'required|string|in:basic,premium',
            ]);
            $vendorId = $data['vendor_id'] ;
            
            return response()->json(['success' => true, 'vendor_id'=>$vendorId]);
        }

        
        if ($step == 4) {
            try {
                $vendorId = Cache::get('vendor_id'); 
                $vendorId = session('vendor_id');
                $rules = (new LabRequest())->rules(); // Get rules from UserRequest
                $validator = Validator::make($request->all(), $rules);  
            
                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors()
                    ], 422);
                }
                $data = $request->all();
                $data['vendor_id'] = $vendorId;

                $lab = Lab::create($data);
                
                if (!empty($request->tax_id)) {
                    $taxMappingData = collect($request->tax_id)->map(function ($taxId) use ($lab) {
                        return [
                            'lab_id' => $lab->id,
                            'tax_id' => $taxId,
                        ];
                    })->toArray();
            
                    LabTaxMapping::insert($taxMappingData);
                }
                $days = [
                    ['day' => 'monday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                    ['day' => 'tuesday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                    ['day' => 'wednesday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                    ['day' => 'thursday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                    ['day' => 'friday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                    ['day' => 'saturday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                    ['day' => 'sunday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => true, 'breaks' => []],
                ];            
                foreach ($days as $key => $val) {
    
                    $val['lab_id'] = $lab->id;
                    LabSession::create($val);
                }
                
                $mediaFields = ['logo', 'license_document', 'accreditation_certificate'];
                foreach ($mediaFields as $field) {
                    if ($request->hasFile($field)) {
                        storeMediaFile($lab, $request->file($field), $field);
                    }
                }
            
                return response()->json(['success' => true, 'message' => __('messages.lab_create'),'lab_id'=>$lab->id,'vendor_id'=>$lab->vendor_id]);
            
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => __('messages.error_creating_lab') . $e->getMessage()
                ], 500); 
            }
            
        }

       
        if ($step == 5) {
            $rules = (new CatlogManagementRequest())->rules(); 
                unset($rules['status']);
                unset($rules['equipment']);
                unset($rules['type']);
                unset($rules['type.*']);  
                
                $validator = Validator::make($request->all(), $rules);
            
                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors()
                    ], 422);
                }
                $data = $request->all();
            $data = $request->all();
          $data['vendor_id']= $data['vendor_id']??auth()->id();
             
                if (isset($data['equipment']) && is_array($data['equipment'])) {
                    $data['equipment'] = json_encode($data['equipment']);
                }

                if (isset($data['type']) && is_array($data['type'])) {
                    $data['type'] = json_encode($data['type']);
                }
                $data['status'] = 1;

                $catlogmanagement = CatlogManagement::create($data);

                
                if ($request->hasFile('test_image')) {
                    storeMediaFile($catlogmanagement, $request->file('test_image'), 'test_image');
                }

                if ($request->hasFile('guidelines_pdf')) {
                    storeMediaFile($catlogmanagement, $request->file('guidelines_pdf'), 'guidelines_pdf');
                }
                return response()->json(['success' => true]);
         }


     
    }

    public function selectPlan(Request $request)
    {
        $planId = $request->input('plan_id');
        $planName = $request->input('plan_name');
        $subscriptionType= $request->input('subscription_type')??null;
        $vendor_id= $request->input('vendor_id');
        $plans = Plan::all();
        $view = view('vendor::backend.VendorRegistration.subscriptionPayment', compact('plans', 'subscriptionType','planId','vendor_id'))->render();
        return response()->json(['success' => true, 'view' => $view]);
    }

    public function getPaymentDetails(Request $request)
    {
        $planId = $request->input('plan_id');
        $plan = Plan::find($planId);
        $discount_percentage = $plan->discount_percentage;

        $discount_amount= ($discount_percentage*$plan->price)/100;
        $baseAmount = $plan->total_price;

        $totalAmount = $baseAmount;
        $total = $totalAmount ;

        return response()->json([
            'price'=>$plan->price,
            'subtotal' => $baseAmount,
            'discount' => $plan->discount ?? 0,
            'discount_percentage' => $discount_percentage ?? 0,
            'discount_amount'=>$discount_amount ?? 0,
            'total' => $total,
            
        ]);
    }

}
