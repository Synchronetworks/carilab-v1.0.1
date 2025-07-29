<?php

namespace Modules\Lab\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Lab\Models\Lab;
use App\Models\Setting;

use Modules\Lab\Transformers\LabResource;
use Modules\Lab\Transformers\LabDetailResource;
class LabsApiController extends Controller
{
  public function labList(Request $request)
  {
    $perPage = $request->input('per_page', 10);
    $searchTerm = $request->input('search', null);
   
    $labs = Lab::where('status', 1);
    if($searchTerm) {
        $labs = $labs->where('name', 'like', '%'.$searchTerm.'%');
    }
    if($request->has('test_case_id')) {
        $labs = $labs->whereHas('testcase', function($query) use ($request) {
            $query->where('id', $request->test_case_id)->orWhere('parent_id', $request->test_case_id);
        });
    }
    if($request->has('test_package_id')) {
        $labs = $labs->whereHas('testpackage', function($query) use ($request) {
            $query->where('id', $request->test_package_id)->orWhere('parent_id', $request->test_package_id);;
        });
    }
    if ($request->has('city_id')) {
        $labs = $labs->where('city_id', $request->city_id);
    }
    if($request->has('latitude') && $request->has('longitude')) {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radious = Setting::getSettings('radious') ?? null;

        if (!is_null($radious) && is_numeric($radious)) {
            $labs = $labs->selectRaw("*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance", [
                    $latitude, $longitude, $latitude
                ])
                ->having('distance', '<', $radious)
                ->orderBy('distance');
        }
    }
    $labs = $labs->paginate($perPage);

    $responseData = LabResource::collection($labs);

    return response()->json([
        'status' => true,
        'data' => $responseData,
        'message' => __('messages.labs_list'),
    ], 200);

  }
  public function labDetail(Request $request)
  {
      $lab = Lab::find($request->id);

      if (!$lab) {
          return response()->json([
              'status' => false,
              'message' => __('messages.lab_not_found'),
          ], 404);
      }

      $responseData = new LabDetailResource($lab);

      return response()->json([
          'status' => true,
          'data' => $responseData,
          'message' => __('messages.lab_details_retrived_successfully'),
      ], 200);
  }
}
