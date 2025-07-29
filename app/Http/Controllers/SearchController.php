<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Constant\Models\Constant;
use Modules\Lab\Models\Lab;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\PlanLimitation;
use Modules\Tax\Models\Tax;
use Modules\Banner\Models\Banner;
use Modules\FAQ\Models\FAQ;
use Modules\Page\Models\Page;
use Modules\Category\Models\Category;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Commision\Models\Commision;
use Modules\Appointment\Models\Appointment;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Document\Models\Document;
use Modules\Coupon\Models\Coupon;
use Modules\Vendor\Models\VendorDocument;
use Modules\Collector\Models\CollectorDocument;
use Modules\Bank\Models\Bank;
use Modules\PrescriptionModels\Prescription;
use Modules\Helpdesk\Models\Helpdesk;
use Modules\Lab\Models\LabSession;
use Currency;
class SearchController extends Controller
{
    public function get_search_data(Request $request)
    {
        $is_multiple = isset($request->multiple) ? explode(',', $request->multiple) : null;
        if (isset($is_multiple) && count($is_multiple)) {
            $multiplItems = [];
            foreach ($is_multiple as $key => $value) {
                $multiplItems[$key] = $this->getData(collect($request[$value]));
            }

            return response()->json(['status' => 'true', 'results' => $multiplItems]);
        } else {
            return response()->json(['status' => 'true', 'results' => $this->getData($request->all())]);
        }
    }
    protected function getData($request)
    {
        $items = [];

        $type = $request['type'];
        $sub_type = $request['sub_type'] ?? null;

        $keyword = $request['q'] ?? null;

        switch ($type) {

            case 'country':

                $items = Country::select('id', 'name as text');


                if ($keyword != '') {

                    $items->where('name', 'LIKE', '%' . $keyword . '%');
                }

                $items = $items->get();
                break;

            case 'state':

                $items = State::select('id', 'name as text');

                if ($sub_type != null) {

                    $items = State::where('country_id', $sub_type)->select('id', 'name as text');
                }

                if ($keyword != '') {

                    $items->where('name', 'LIKE', '%' . $keyword . '%');
                }

                $items = $items->get();
                break;

            case 'city':

                $items = City::select('id', 'name as text');

                if ($sub_type != null) {

                    $items = City::where('state_id', $sub_type)->select('id', 'name as text');
                }

                if ($keyword != '') {

                    $items->where('name', 'LIKE', '%' . $keyword . '%');
                }

                $items = $items->get();
                break;


            case 'customers':
                $items = User::role('user')->select('id', \DB::raw("CONCAT(first_name,' ',last_name) AS text"));
                if ($keyword != '') {
                    $items->where(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $keyword . '%');
                }
                $items = $items->limit(50)->get();
                break;

            case 'earning_payment_method':
                $query = Constant::getAllConstant()
                    ->where('type', 'EARNING_PAYMENT_TYPE');
                foreach ($query as $key => $data) {
                    if ($keyword != '') {
                        if (strpos($data->name, str_replace(' ', '_', strtolower($keyword))) !== false) {
                            $items[] = [
                                'id' => $data->name,
                                'text' => $data->value,
                            ];
                        }
                    } else {
                        $items[] = [
                            'id' => $data->name,
                            'text' => $data->value,
                        ];
                    }
                }
                break;



            case 'time_zone':
                $items = timeZoneList();

                $data = [];
                $i = 0;
                foreach ($items as $key => $row) {
                    $data[$i] = [
                        'id' => $key,
                        'text' => $row,
                    ];

                    $i++;
                }

                $items = $data;

                break;

            case 'additional_permissions':
                $query = Constant::getAllConstant()
                    ->where('type', 'additional_permissions');
                foreach ($query as $key => $data) {
                    if ($keyword != '') {
                        if (strpos($data->name, str_replace(' ', '_', strtolower($keyword))) !== false) {
                            $items[] = [
                                'id' => $data->name,
                                'text' => $data->value,
                            ];
                        }
                    } else {
                        $items[] = [
                            'id' => $data->name,
                            'text' => $data->value,
                        ];
                    }
                }

                break;

            case 'constant':
                $query = Constant::getAllConstant()->where('type', $sub_type);
                foreach ($query as $key => $data) {
                    if ($keyword != '') {
                        if (strpos($data->name, str_replace(' ', '_', strtolower($keyword))) !== false) {
                            $items[] = [
                                'id' => $data->name,
                                'text' => $data->value,
                            ];
                        }
                    } else {
                        $items[] = [
                            'id' => $data->name,
                            'text' => $data->value,
                        ];
                    }
                }

                break;

            case 'role':
                $query = Role::all();
                foreach ($query as $key => $data) {
                    if ($keyword != '') {
                        if (strpos($data->name, str_replace(' ', '_', strtolower($keyword))) !== false) {
                            $items[] = [
                                'id' => $data->id,
                                'text' => $data->name,
                            ];
                        }
                    } else {
                        $items[] = [
                            'id' => $data->id,
                            'text' => $data->name,
                        ];
                    }
                }

                break;

            case 'lab_name':
                if(auth()->user()->user_type == 'vendor'){
                    $items = Lab::where('vendor_id',auth()->id())->select('id', 'name as text');
                }
                else{
                    $items = Lab::select('id', 'name as text');
                }
                if ($keyword != '') {
                    $items->where('name', 'LIKE', '%' . $keyword . '%');
                }
                $items = $items->get();
                break;
            case 'vendor':
                $items = User::where('user_type', 'vendor')->select('id', \DB::raw("CONCAT(first_name,' ',last_name) AS text"));
                if ($keyword != '') {
                    $items->where(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $keyword . '%');
                }
                $items = $items->limit(50)->get();
                break;
                case 'collector_name':
                    $user = auth()->user(); // Get the logged-in user
                
                    $items = User::where('user_type', 'collector')
                        ->select('id', \DB::raw("CONCAT(first_name, ' ', last_name) AS text"));
                
                    
                    if ($user->user_type == 'vendor') {
                        $items->whereHas('collectorVendormapping', function ($query) use ($user) {
                            $query->where('vendor_id', $user->id);
                        });
                    }
                
                   
                    if ($keyword != '') {
                        $items->where(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $keyword . '%');
                    }
                
                    $items = $items->limit(50)->get();
                    break;
            case 'test':
                if(auth()->user()->user_type == 'vendor'){
                    $items =  CatlogManagement::where('vendor_id',auth()->id())->select('id', 'name as text');
                }else{
                    $items =  CatlogManagement::select('id', 'name as text');
                }
                if ($keyword != '') {
                    $items->where(\DB::raw("name"), 'LIKE', '%' . $keyword . '%');
                }
                $items = $items->limit(50)->get();
                break;
            case 'tax':

                $items =  Tax::select('id', 'title as text');
                if ($keyword != '') {
                    $items->where(\DB::raw("title"), 'LIKE', '%' . $keyword . '%');
                }
                $items = $items->limit(50)->get();
                break;
            
                case 'test_type':
                    $constants = Constant::where('type', 'test_type')->get();
                    foreach ($constants as $constant) {
                        if ($keyword != '') {
                            if (strpos(strtolower($constant->value), strtolower($keyword)) !== false) {
                                $items[] = [
                                    'id' => $constant->name,
                                    'text' => $constant->value,
                                ];
                            }
                        } else {
                            $items[] = [
                                'id' => $constant->name,
                                'text' => $constant->value,
                            ];
                        }
                    }
                    break;
        
            case 'category':
                $items = Category::select('id', 'name as text');
                if ($keyword != '') {
                    $items->where('name', 'LIKE', '%' . $keyword . '%');
                }
                $items = $items->limit(50)->get();
                break;
            case 'commission':
                $items = Commision::select('id', 'title', 'type', 'value')
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where('title', 'LIKE', '%' . $keyword . '%');
                })
                ->when($request['role'] == 'vendor', function ($q) use ($request) {
                    $q->where('user_type', $request['role']);
                })
                ->limit(50)
                ->get()
                ->map(function ($commission) {
                    $formattedValue = $commission->type == 'Percentage'
                        ? "{$commission->value}%"
                        : Currency::format($commission->value); 
    
                    return [
                        'id'   => $commission->id,
                        'text' => "{$commission->title} ({$formattedValue})"
                    ];
                });
            break;

        }

        return $items;
    }
    public function check_in_trash(Request $request)
    {
        $ids = $request->ids;
        $type = $request->datatype;
        $InTrash = 0;

        switch ($type) {
            case 'plan':
                $InTrash = Plan::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'plan-limitation':
                $InTrash = PlanLimitation::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'users':
                $InTrash = User::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'taxes':
                $InTrash = Tax::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'constant':
                $InTrash = Constant::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'faqs':
                $InTrash = FAQ::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'catlogmanagement':
                $InTrash = CatlogManagement::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
           
            case 'pages':
                $InTrash = Page::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'appointment':
                $InTrash = Appointment::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'lab':
                $InTrash = Lab::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'labsession':
                $InTrash = LabSession::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'packagemanagement':
                $InTrash = PackageManagement::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'pages':
                $InTrash = Page::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'document':
                $InTrash = Document::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'coupons':
                $InTrash = Coupon::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'vendorDocument':
                $InTrash = VendorDocument::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'collectordocument':
                $InTrash = CollectorDocument::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'category':
                $InTrash = Category::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'bank':
                $InTrash = Bank::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'commision':
                $InTrash = Commision::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'reviews':
                $InTrash = Review::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'prescription':
                $InTrash = Prescription::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'banner':
                $InTrash = Banner::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'helpdesk':
                $InTrash = Helpdesk::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'notification':
                $InTrash = Notification::whereIn('id', $ids)->get();
                break;
            default:
                break;
        }

        if (!is_array($ids)) {
            $ids = (array) $ids; 
        }
        
        if ($ids != null) {
            
            if (is_object($InTrash) && method_exists($InTrash, 'count')) {
                if ($InTrash->count() === count($ids)) {
                    return response()->json(['all_in_trash' => true]);
                }
            } elseif (is_array($InTrash)) {
                if (count($InTrash) === count($ids)) {
                    return response()->json(['all_in_trash' => true]);
                }
            }
        }
        return response()->json(['all_in_trash' => false]);
    }
}
