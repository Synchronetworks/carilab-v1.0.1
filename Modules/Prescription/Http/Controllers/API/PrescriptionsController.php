<?php

namespace Modules\Prescription\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Prescription\Models\Prescription;
use Modules\Prescription\Http\Requests\PrescriptionRequest;
use Modules\Prescription\Transformers\PrescriptionResource;
use Modules\Prescription\Transformers\PrescriptionDetailResource;
class PrescriptionsController extends Controller
{
  // api controller logic
  public function prescriptionList(Request $request){
    $prescriptions = Prescription::myPrescription()->with('user','prescriptionPackages','labMappings');
    if($request->has('user_id')){
      $prescriptions = $prescriptions->where('user_id',$request->user_id);
    }
    if($request->has('prescription_status')){
      $prescriptions = $prescriptions->where('prescription_status',$request->prescription_status);
    }
    $per_page = config('constant.PER_PAGE_LIMIT');
    if( $request->has('per_page') && !empty($request->per_page)){
        if(is_numeric($request->per_page)){
            $per_page = $request->per_page;
        }
        if($request->per_page === 'all' ){
            $per_page = $payment->count();
        }
    }
    $prescriptions = $prescriptions->orderBy('id','desc')->paginate($per_page);
    $items = PrescriptionResource::collection($prescriptions);

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
  }
  public function prescriptionDetail (Request $request){
      $prescriptions = Prescription::with('user','prescriptionPackages','labMappings');
      
      if($request->has('id')){
        $id = $request->id;
        $prescriptions = $prescriptions->where('id', $id)->first();
      }

      $responseData = new PrescriptionDetailResource($prescriptions);

      return response()->json([
          'status' => true,
          'data' => $responseData,
          'message' => __('messages.prescription_details_retrive'),
      ], 200);
  }
}
