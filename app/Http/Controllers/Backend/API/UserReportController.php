<?php

namespace App\Http\Controllers\Backend\API;


use App\Http\Controllers\Controller;
use Modules\Report\Models\Report;
// use Modules\Report\Models\ReportFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\UserReport;
use App\Models\User;
use App\Http\Resources\UserReportResource;

use Illuminate\Http\Request;

class UserReportController extends Controller
{
   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'uploaded_at' => 'required|date_format:Y-m-d H:i:s',
            'additional_notes' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }
    
        $report = UserReport::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'uploaded_at' => $request->uploaded_at,
            'additional_notes' => $request->additional_notes,
        ]);

        if($request->has('attachment_count')) {
            for($i = 0 ; $i < $request->attachment_count ; $i++){
                $attachment = "medical_report_".$i;
                if($request->$attachment != null){
                    $file[] = $request->$attachment;
                }
            }
            storeMediaFile($report,$file, 'medical_report');
        }
    
        return response()->json([
            'status' => true,
            'message' => __('messages.report_add')
        ], 200);
    }

    public function updateReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'uploaded_at' => 'required|date_format:Y-m-d H:i:s',
            'additional_notes' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }
    
        $report = UserReport::findOrFail($id);
        $report->update($request->only('name', 'uploaded_at', 'additional_notes'));
    
        if($request->has('attachment_count')) {
            for($i = 0 ; $i < $request->attachment_count ; $i++){
                $attachment = "medical_report_".$i;
                if($request->$attachment != null){
                    $file[] = $request->$attachment;
                }
            }
            storeMediaFile($report,$file, 'medical_report');
        }
    
        return response()->json([
            'status' => true,
            'message' => __('messages.report_update')
        ], 200);
    }
    public function destroy($id)
    {
        $report = UserReport::find($id);
    
        if (!$report) {
            return response()->json([
                'status' => false,
                'message' => __('messages.report_not_found')
            ], 404);
        }
    
        $report->delete();
    
        return response()->json([
            'status' => true,
            'message' => __('messages.report_delete')
        ], 200);
    }

    public function getReportList(Request $request)
    {
        try {
            // Get the authenticated user ID or fallback to request user_id
            
            $userId =  $request->user_id ?? auth()->id();
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
            $medicalreport = UserReport::where('user_id', $userId);
            $per_page = config('constant.PER_PAGE_LIMIT');
            if( $request->has('per_page') && !empty($request->per_page)){
                if(is_numeric($request->per_page)){
                    $per_page = $request->per_page;
                }
                if($request->per_page === 'all' ){
                    $per_page = $medicalreport->count();
                }
            }

            $medicalreport = $medicalreport->orderBy('id','desc')->paginate($per_page);
            $items = UserReportResource::collection($medicalreport);

            $response = [
                'pagination' => [
                    'total_items' => $items->total(),
                    'per_page' => $items->perPage(),
                    'currentPage' => $items->currentPage(),
                    'totalPages' => $items->lastPage(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                    'next_page' => $items->nextPageUrl(),
                    'previous_page' => $items->previousPageUrl(),
                ],
                'data' => $items,
            ];
            
            return comman_custom_response($response);

        } catch (\Throwable $e) { // Using `\Throwable` to catch all errors (including fatal errors)
            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => __('messages.something_went_wrong'),
                'error'   => $e->getMessage(), // Consider logging this instead of returning in production
            ], 500);
        }
    }

}
