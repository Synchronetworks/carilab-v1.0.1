<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\Category\Models\Category;
use Modules\Category\Transformers\CategoryResource;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\PackageManagement\Transformers\PackageResource;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\CatlogManagement\Transformers\CatlogResource;
use Modules\Lab\Models\Lab;
use Modules\Lab\Transformers\LabResource;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\CashPaymentHistories;
use Modules\Banner\Models\Banner;
Use Modules\Banner\Transformers\BannerResource;
use Modules\Commision\Models\CommissionEarning;
use Modules\Payout\Models\Payout;
use Modules\Review\Transformers\ReviewResource;
use Modules\Review\Models\Review;
use Modules\Bank\Models\Bank;


class DashboardController extends Controller
{
    protected $recommendationService;
    public function dashboardDetail(Request $request){
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $userId = $request->input('user_id') ?? optional(auth()->user())->id ?? null;
        $popularPackageList = null;
        $user = User::where('id',$userId)->first();
        

        $notification=0;
        
        if($user){
            $notification = $user->unreadNotifications->count();
        }
        $sliderList = BannerResource::collection(Banner::where('status',1)->get());
        $lab = new Lab();
        $nearByLabList = collect(); // Initialize as an empty collection
        
        if ($request->has('city_id')) {
            $nearByLabList = $lab->where('city_id', $request->city_id)->get();
        }
        
        if ($latitude && $longitude) {
            $nearByLabList = $lab->getNearestlab($latitude, $longitude); // Assuming this returns a collection
        }
        
        $nearByLabList = LabResource::collection($nearByLabList);
        $categoryList = Category::where('status', 1)->take(6)->orderBy('id','desc')->get();
        $categoryList = CategoryResource::collection($categoryList);

        $upcomingAppointmentList = null;
        if ($userId) {
            $upcomingAppointmentList = Appointment::where('appointment_date', '>=', Carbon::now())
            ->where('customer_id', $userId)
            ->orderBy('id','desc')
            ->first();
        }

        $nearByLabIds = $nearByLabList ? $nearByLabList->pluck('id')->toArray() : [];

        $featuredTestCaseList = $nearByLabIds 
        ? CatlogManagement::where('is_featured', 1)->whereNull('parent_id')->whereIn('lab_id', $nearByLabIds)->latest('updated_at')->take(3)->get() 
        : CatlogManagement::where('is_featured', 1)->whereNull('parent_id')->orderBy('updated_at','desc')->take(3)->get();
        
        $featuredTestCaseList = CatlogResource::collection($featuredTestCaseList);

        $popularPackageList = $nearByLabIds 
        ? PackageManagement::where('is_featured', 1)->whereNull('parent_id')->whereIn('lab_id', $nearByLabIds)->latest('updated_at')->take(3)->get()
        : PackageManagement::where('is_featured', 1)->whereNull('parent_id')->orderBy('updated_at','desc')->take(3)->get();
       
        $popularPackageList = PackageResource::collection($popularPackageList);
        

        $data = [
            "status" => true,
            "slider_list" => $sliderList,
            "near_by_lab_list" => $nearByLabList,
            "category_list" => $categoryList,
            "upcoming_appointment_list" => $upcomingAppointmentList,
            "featured_test_case_list" => $featuredTestCaseList,
            "popular_package_list" => $popularPackageList,
            'notification_unread_count' => $notification,
        ];
     
        return response()->json($data);
    }


    public function CollectordashboardDetail(Request $request){

        $collectorId = $request->input('collector_id') ?? auth()->id();
        $collector = User::with('collectorVendormapping.vendor')->find($collectorId);
        $vendor = $collector->collectorVendormapping;
        if($vendor){
            $vendor_info = [
                'id' => optional($vendor->vendor)->id ?? null,
                'full_name' =>optional($vendor->vendor)->first_name .' '. optional($vendor->vendor)->last_name ?? null,
                'profile_image' => setBaseUrlWithFileName(optional($vendor->vendor)->profile_image),    
                'mobile'=>optional($vendor->vendor)->mobile ?? null,
                'email'=>optional($vendor->vendor)->email ?? null,
                'is_verified' => optional($vendor->vendor)->is_verify ?? 0,
            ];
        }
        if($collector){
            $get_current_time = Carbon::now();
            $collector->last_online_time = $get_current_time->toTimeString();
            $collector->update();
        }

        $totalAppointments = Appointment::whereHas('appointmentCollectorMapping', function ($query) use ($collectorId) {
            $query->where('collector_id', $collectorId);
        })->withTrashed()->count();

        $complateAppointments = Appointment::whereHas('appointmentCollectorMapping', function ($query) use ($collectorId) {
            $query->where('collector_id', $collectorId);
        })->where('status','completed')->withTrashed()->count();

        $total_revenue=Payout::where('user_id', $collectorId)->sum('amount');
        $remaning_payout_amount=CommissionEarning::where('employee_id', $collectorId)->where('commission_status','unpaid')->sum('commission_amount');

        $revenuedata = Payout::selectRaw('sum(amount) as total , DATE_FORMAT(updated_at , "%m") as month' )
                        ->where('user_id',$collectorId)
                        ->where('user_type','collector')
                        ->whereYear('updated_at',date('Y'))
                        ->groupBy('month');
        $revenuedata= $revenuedata->get();
        $data['revenueData']    =    [];
        for($i = 1; $i <= 12; $i++ ){
            $revenueData = 0.0;
            foreach($revenuedata as $revenue){
                if($revenue['month'] == $i){

                    $data['revenueData'][] = [
                        $i => $revenue['total']
                    ];
                    $revenueData++;
                }
            }
            if($revenueData == 0){
                $data['revenueData'][] = (object) [] ;
            }
        }

        $notification = count($collector->unreadNotifications);

        $reviews = ReviewResource::collection(
            Review::where('collector_id', $collectorId)
                ->orderByDesc('rating')
                ->take(5)
                ->get()
          );
          $cashpaymenthistroy = new CashPaymentHistories;
          $total_cash_in_hand = $cashpaymenthistroy->total_cash_in_hand($collectorId);

          $data = [
            'vendor_info' => $vendor_info ?? [],
            "total_appointments" => $totalAppointments ?? 0,
            "complate_appointment" => $complateAppointments ?? 0,
            'total_cash_in_hand'   => $total_cash_in_hand ?? 0,
            "total_revenue" => $total_revenue ?? 0,
            'monthly_revenue'               => $data['revenueData'] ?? [],
            'notification_unread_count'     => $notification ?? 0,
            "remaning_payout_amount" => $remaning_payout_amount ?? 0,
            "reviews" => $reviews ?? []
        ];
     
        return response()->json($data);

    }

    public function searchList(Request $request)
    {
        $query = $request->input('search');
        
        $results = [];

        // Search in vendor // Need To Add Role Base
        $results['vendor'] = User::where('user_type','vendor')
        ->where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%");
        })
        ->get();
    
    // Search for collectors
    $results['collector'] = User::where('user_type','collector')
        ->where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%");
        })
        ->get();

        
        // Search in Categories
        $categories = Category::where('name', 'like', "%{$query}%")->get();
        $results['categories'] = $categories;

        // Search in Lab
        $Lab = Lab::where('name', 'like', "%{$query}%")->get();
        $results['Lab'] = $Lab;

         // Search in testcase
         $testcase = CatlogManagement::where('name', 'like', "%{$query}%")->get();
         $results['test_case'] = $testcase;

         // Search in testpackage
         $testpackage = PackageManagement::where('name', 'like', "%{$query}%")->get();
         $results['test_package'] = $testpackage;

        return response()->json($results);
    }
    public function defaultBank(Request $request)
    {
        $bank_id = $request->id;

        // Check if the bank exists
        $bank = Bank::find($bank_id);

        if ($bank) {
            // Set all banks' is_default column to 0
            Bank::query()->update(['is_default' => 0]);

            // Set the specified bank's is_default column to 1
            $bank->update(['is_default' => 1]);

            $message = __('messages.bank_default_set');
            return response()->json(['status' => true, 'message' => $message]);
        } else {
            $message = __('messages.bank_not_found');
            return response()->json(['status' => false, 'message' => $message]);
        }
    }  
}
