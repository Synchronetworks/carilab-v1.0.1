<?php

namespace Modules\Appointment\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Trait\NotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Appointment\Transformers\AppointmentResource;
use Modules\Appointment\Transformers\AppointmentDetailResource;
use Modules\Commision\Models\CommissionEarning;
use App\Models\User;
use Modules\Review\Transformers\ReviewResource;
use Modules\Review\Models\Review;
use Currency;
use Modules\Appointment\Models\CashPaymentHistories;
use Modules\Appointment\Models\AppointmentActivity;
use Modules\Appointment\Models\AppointmentStatus;
use Modules\Appointment\Models\LiveLocation;
use Modules\Appointment\Models\AppointmentPackageMapping;
use App\Models\Setting;

class AppointmentAPIController extends Controller
{
    use AppointmentTrait;
    use NotificationTrait;  
  public function getAppointments(Request $request)
  {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', null);
     
        $appointments = Appointment::myAppointment();
        
        $filters = [
            'collector_id' => ['relation' => 'appointmentCollectorMapping', 'column' => 'collector_id'],
            'customer_id' => ['relation' => null, 'column' => 'customer_id'],
            'vendor_id' => ['relation' => null, 'column' => 'vendor_id'],
            'status' => ['relation' => null, 'column' => 'status'],
            'payment_status' => ['relation' => 'transactions', 'column' => 'payment_status']
        ];
        
        foreach ($filters as $key => $filter) {
            if ($request->has($key) && !empty($request->$key)) {
                $values = is_array($request->$key) ? $request->$key : explode(',', $request->$key);
        
                if ($filter['relation']) {
                    $appointments->whereHas($filter['relation'], function ($query) use ($filter, $values) {
                        $query->whereIn($filter['column'], $values);
                    });
                } else {
                    $appointments->whereIn($filter['column'], $values);
                }
            }
        }
        if ($request->has('test_id') && !empty($request->test_id) && $request->has('test_type') && !empty($request->test_type)) {
            $test_ids = is_array($request->test_id) ? $request->test_id : explode(',', $request->test_id);
            $appointments->where('test_type', $request->test_type)->whereIn('test_id', $test_ids);
        }

        if($searchTerm) {
            $appointments->where(function ($query) use ($searchTerm) {
                $query->where('id', 'LIKE', "%$searchTerm%")

                    ->orWhereHas('getTestAttribute', function ($serviceQuery) use ($searchTerm) {
                        $serviceQuery->where('name', 'LIKE', "%$searchTerm%");
                    })

                    ->orWhereHas('appointmentCollectorMapping', function ($collectorQuery) use ($searchTerm) {
                        $collectorQuery->WhereHas('collector', function ($collector) use ($searchTerm) {
                            $collector->where(function ($nameQuery) use ($searchTerm) {
                                $nameQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$searchTerm%"])
                                    ->orWhere('email', 'LIKE', "%$searchTerm");
                            });
                        });
                     })

                     ->orWhereHas('customer', function ($userQuery) use ($searchTerm) {
                        $userQuery->where(function ($nameQuery) use ($searchTerm) {
                            $nameQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$searchTerm%"])
                                ->orWhere('email', 'LIKE', "%$searchTerm");
                        });
                    });
            });
        }
        if(!empty($request->date_start) && !empty($request->date_end)){
            $appointments->whereBetween('appointment_date', [$request->date_start, $request->date_end]);
        }
        $appointments = $appointments->orderBy('updated_at','desc')->paginate($perPage);
        $responseData = AppointmentResource::collection($appointments);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.appointment_list')
        ], 200);
  }
  public function getActivities(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', null);

        $activity = AppointmentActivity::where('appointment_id', $request->id)
                    ->orderBy('id', 'desc')->get(); 
        $activity->transform(function ($activity) {
            $dateTime = Setting::timeZone($activity->activity_date);
            $activity->activity_date = Setting::formatDate($dateTime) . ' ' . Setting::formatTime($dateTime);
            return $activity;
        });
        return response()->json([
            'status' => true,
            'data' => $activity,
            'message' => __('appointment_activity_list')
        ], 200);
    }

  public function appointmentStatus(Request $request)
  {
      $appointment_status = AppointmentStatus::orderBy('sequence')->get();
      return comman_custom_response($appointment_status);
  }

  public function appointmentDetail(Request $request)
  {
      $appointments = Appointment::where('id',$request->id)->with('appointmentCollectorMapping','address')->first();

      if (!$appointments) {
          return response()->json([
              'status' => false,
              'message' => __('messages.appointment_not_found')
          ], 404);
      }
        $lab_id = !empty($request->lab_id) ? $request->lab_id : $appointments->lab_id;

        $collector_id = !empty($request->collector_id) ? $request->collector_id : optional($appointments->appointmentCollectorMapping)->collector_id;
        
        $appointments['lab_review'] = ReviewResource::collection(
              Review::where('lab_id', $lab_id)
                  ->where('user_id',$appointments->customer_id)
                  ->whereNotNull('review') 
                  ->orderByDesc('updated_at')
                  ->take(1)
                  ->get()
          );


        $appointments['collector_review'] = ReviewResource::collection(
            Review::where('collector_id', $collector_id)
                ->where('user_id',$appointments->customer_id)
                ->whereNotNull('review') 
                ->orderByDesc('updated_at')
                ->take(1)
                ->get()
        );
       
        
    

      $responseData = new AppointmentDetailResource($appointments);

      return response()->json([
          'status' => true,
          'data' => $responseData,
          'message' => __('messages.appointment_details_retrive'),
      ], 200);
  }


 

    public function updateLocation(Request $request) {
        $appointmentID = $request->input('appointment_id');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');


        $data = [
            'appointment_id' => $appointmentID,
            'latitude' => $latitude,
            'longitude' => $longitude,

        ];
        $locations = LiveLocation::updateOrCreate(['appointment_id' => $data['appointment_id']], $data);
        $time_zone=$this->getTimeZone();

        $datetime_in_timezone = Carbon::parse($locations->updated_at)->timezone($time_zone);

        $data['datetime'] = $datetime_in_timezone->toDateTimeString();

        $message = __('messages.location_update');
        return response()->json(['data' => $data, 'message' => $message], 200);
    }

    public function getLocation(Request $request){
        $appointmentID = $request->input('appointment_id');

        $latestLiveLocation = Cache::remember('latest_live_location_' . $appointmentID, 30, function () use ($appointmentID) {
            return LiveLocation::where('appointment_id', $appointmentID)
                ->latest()
                ->first();
        });
        if (!$latestLiveLocation) {
            return response()->json(['error' => __('messages.live_location_not_found_appointment_id')], 404);
        }

        $time_zone=$this->getTimeZone();

        $datetime_in_timezone = Carbon::parse($latestLiveLocation->updated_at)->timezone($time_zone);

        $datetime= $datetime_in_timezone->toDateTimeString();
        $data = [
            'latitude' => $latestLiveLocation->latitude,
            'longitude' => $latestLiveLocation->longitude,
            'datetime' =>  $datetime,
        ];

        $message = __('messages.location_update');
        return response()->json(['data' => $data, 'message' => $message], 200);

    }
    
    public function collectedTestCase(Request $request) {
        $appointmentID = $request->input('appointment_id');
        $test_ids = $request->input('test_ids'); 
        $package_id = $request->input('package_id');
    
        
        $appointmentPackageMappings = AppointmentPackageMapping::where('appointment_id', $appointmentID)
            ->orWhere('package_id', $package_id)
            ->get();
    
        
        if ($appointmentPackageMappings->isEmpty()) {
            return response()->json(['error' => __('package_not_found_appointment')], 404);
        }
    
      
        $updatedRows = AppointmentPackageMapping::where('appointment_id', $appointmentID)
            ->whereIn('test_id', $test_ids)
            ->update(['status' => 1]);
    
        if ($updatedRows > 0) {
            return response()->json(['message' => __('messages.test_case_collected')], 200);
        } else {
            return response()->json(['error' => __('messages.no_found_match_test_case')], 404);
        }
    }    
}
