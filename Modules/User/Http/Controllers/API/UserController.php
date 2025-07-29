<?php

namespace Modules\User\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Transformers\UserProfileResource;
use App\Models\User;
use App\Models\UserOtherMapping;
use App\Models\UserAddressMapping;
use Modules\Review\Models\Review;
use Modules\Commision\Models\Commision;
use Modules\Lab\Models\Lab;
use Modules\Review\Transformers\ReviewResource;

class UserController extends Controller
{
    public function profileDetails(Request $request)
    {
        $userId = $request->user_id ?? auth()->id();
        
        $user = User::with(['collectorVendormapping.vendor', 'userCommissionMapping'])->where('id',$userId)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        $responseData = new UserProfileResource($user);
        $userType = $user->user_type;

        // Fetch appointment stats dynamically
        $appointmentStats = $user->getAppointmentStats($userId, $userType);
        $responseData = array_merge($responseData->toArray(request()), $appointmentStats);

        // Collector-specific data
        if ($userType === 'collector') {
            $responseData['education'] = optional($user->collector)->education ?? null;
            $responseData['degree'] = optional($user->collector)->degree ?? null;
            $responseData['bio'] = optional($user->collector)->bio ?? null;
            $responseData['yesr_of_experienc'] = optional($user->collector)->experience ?? null;
            $review_list = Review::where('collector_id', $userId)->get();
            $responseData['review_list'] = ReviewResource::collection($review_list);
            $responseData['total_reviews'] = $review_list->count();
            $responseData['rating'] = $responseData['total_reviews'] > 0 ? Review::where('collector_id', $userId)->avg('rating') : 0;

            // Collector Commission Details
            $responseData['collector_commission'] = $user->userCommissionMapping->map(function ($commissionMapping) {
                return [
                    'commission_name'   => $commissionMapping->commissions ? $commissionMapping->commissions->title : null,
                    'commission_type'   => $commissionMapping->commission_type ?? null,
                    'commission_amount' => $commissionMapping->commission ?? 0,
                ];
            });            
            // Vendor Info (if applicable)
            if ($vendor = optional($user->collectorVendormapping)->vendor) {
                $responseData['vendor_info'] = [
                    'id' => $vendor->id ?? null,
                    'full_name' => trim(($vendor->first_name ?? '') . ' ' . ($vendor->last_name ?? '')),
                    'profile_image' => setBaseUrlWithFileName($vendor->profile_image ?? ''),
                    'mobile' => $vendor->mobile ?? null,
                    'email' => $vendor->email ?? null,
                    'is_verified' => $vendor->is_verify ?? 0,
                ];
            }
        }

        // Vendor-specific data
        if ($user->user_type === 'vendor') {
            $lab = Lab::where('vendor_id', $userId)->get();
            $responseData['lab_list'] = $lab->map(function ($lab) {
                return [
                    'lab_id' => $lab->id,
                    'lab_name' => $lab->name,
                    'lab_logo' => $lab->getLogoUrlAttribute(),
                    'lab_phone' => $lab->phone,
                    'lab_email' => $lab->email,
                    'address' => $lab->address,
                ];
            });
            $responseData['total_labs'] = $lab->count();
            $responseData['total_collectors'] = User::whereHas('collectorVendormapping', function ($q) use ($userId) {
                $q->where('vendor_id', $userId);
            })->count();

            // Vendor Reviews
            $responseData['review_list'] = Review::whereHas('lab.vendor', function ($q) use ($userId) {
                $q->where('vendor_id', $userId);
            })->get();
            $responseData['total_reviews'] = $responseData['review_list']->count();
            $responseData['rating'] = $responseData['total_reviews'] > 0 
                                ? (float) Review::whereHas('lab.vendor', function ($q) use ($userId) {
                                    $q->where('vendor_id', $userId);
                                })->avg('rating') 
                                : 0.0;

        }

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.user_details'),
        ], 200);
    }

    public function userList(Request $request){
        $perPage = $request->input('per_page', 10);
        $user_type = isset($request['user_type']) ? $request['user_type'] : 'collector';
        $users = User::where('user_type',$user_type);
        $users = $users->orderBy('updated_at','desc')->paginate($perPage);
        $responseData = UserProfileResource::collection($users);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.user_details'),
        ], 200);
    }

    public function getOtherMembersList(Request $request)
    {
        try {
            // Get the authenticated user ID or fallback to request user_id
            $userId = auth()->id() ?? $request->user_id;

            // Fetch the user and ensure it exists
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => __('messages.user_not_found'),
                ], 404);
            }

            // Fetch other members for the given user
            $otherMembers = UserOtherMapping::where('user_id', $userId)->get();

            // Return success response
            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => __('messages.records_fetched_successfully'),
                'data'    => $otherMembers, // No need for `?? []` since `.get()` returns an empty collection if no data exists
            ], 200);

        } catch (\Throwable $e) { // Using `\Throwable` to catch all errors (including fatal errors)
            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => __('messages.something_went_wrong'),
                'error'   => $e->getMessage(), // Consider logging this instead of returning in production
            ], 500);
        }
    }


    public function addOtherMember(Request $request)
    {
        // Retrieve all input data
        $data = $request->all();

        // Set the user ID, either from the authenticated user or from the request
        $data['user_id'] = auth()->id() ?? $request->user_id;

        // Check if the user ID is null
        if (is_null($data['user_id'])) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.user_not_found'),
            ], 404);
        }
        $user = User::where('id',$data['user_id'])->first();
        if (is_null($user)) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.user_not_found'),
            ], 404);
        }
        // Determine if it's an update or a new record
        if (!empty($data['id'])) {
            $otherMemberList = UserOtherMapping::find($data['id']);
            if (!$otherMemberList) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => __('messages.record_not_found'),
                ], 404);
            }
            $otherMemberList->update($data);
            $message = __('messages.member_updated');
        } else {
            $otherMemberList = UserOtherMapping::create($data);
            $message = __('messages.member_added');
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            storeMediaFile($otherMemberList, $request->file('profile_image'), 'profile_image');
        }

        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $otherMemberList,
            'message' => $message,
        ], 200);
    }

    public function deleteOtherMember($id)
    {
        
        // Retrieve the record by ID
        $otherMember = UserOtherMapping::find($id);

        // Check if the record exists
        if (!$otherMember) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.record_not_found'),
            ], 404);
        }

        // Delete the record
        $otherMember->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => __('messages.record_deleted_successfully'),
        ], 200);
       
    }
    
    public function addAddress(Request $request)
    {
        // Retrieve all input data
        $data = $request->all();

        // Set the user ID, either from the authenticated user or from the request
        $data['user_id'] = auth()->id() ?? $request->user_id;

        // Check if the user ID is null
        if (is_null($data['user_id'])) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.user_id_found'),
            ], 404);
        }
        $user = User::where('id',$data['user_id'])->first();
        if (is_null($user)) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        // Determine if it's an update or a new record
        if (!empty($data['id'])) {
            $addresslist = UserAddressMapping::find($data['id']);
            if (!$addresslist) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => __('messages.record_not_found'),
                ], 404);
            }
            $addresslist->update($data);
            $message = __('messages.address_updated');
        } else {
            $addresslist = UserAddressMapping::create($data);
            $message = __('messages.address_added');
        }


        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $addresslist,
            'message' => $message,
        ], 200);
    }
    public function getAddressList(Request $request)
    {
        try {
            // Get the authenticated user ID or fallback to request user_id
            $userId = auth()->id() ?? $request->user_id;

            // Fetch the user and ensure it exists
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => __('messages.user_not_found'),
                ], 404);
            }

            // Fetch other members for the given user
            $address = UserAddressMapping::where('user_id', $userId)->get();

            // Return success response
            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => __('messages.records_fetched_successfully'),
                'data'    => $address, // No need for `?? []` since `.get()` returns an empty collection if no data exists
            ], 200);

        } catch (\Throwable $e) { // Using `\Throwable` to catch all errors (including fatal errors)
            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => __('messages.something_went_wrong'),
                'error'   => $e->getMessage(), // Consider logging this instead of returning in production
            ], 500);
        }
    }

    public function deleteAddress($id)
    {
        
        // Retrieve the record by ID
        $address = UserAddressMapping::find($id);

        // Check if the record exists
        if (!$address) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.record_not_found'),
            ], 404);
        }

        // Delete the record
        $address->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => __('messages.record_deleted_successfully'),
        ], 200);
       
    }
    public function collectorAvailable(Request $request){
        $user_id =  $request->user_id;
        $user = User::where('id',$user_id)->first();

        if($user == "") {
            $message = __('messages.user_not_found');
            return comman_custom_response($message,400);
        }
        $user->is_available = $request->is_available;
        $user->save();

        $message = __('messages.update_form',['form' => __('messages.status') ]);
        $response = [
            'data' => new UserProfileResource($user),
            'message' => $message
        ];
        return comman_custom_response($response);
    }
}
