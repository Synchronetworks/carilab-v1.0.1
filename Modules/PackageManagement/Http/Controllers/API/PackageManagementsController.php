<?php

namespace Modules\PackageManagement\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\PackageManagement\Transformers\PackageResource;
use Modules\PackageManagement\Transformers\PackageDetailResource;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\CatlogManagement\Transformers\CatlogResource;
class PackageManagementsController extends Controller
{
  // api controller logic
  public function packageList(Request $request)
  {
      $perPage = $request->input('per_page', 10);
      $searchTerm = $request->input('search', null);
      $minPrice = $request->input('min_price', 0); // Default to 0 if not provided
      $maxPrice = $request->input('max_price', null);
     
      $package = PackageManagement::where('status', 1)->with('packageCatlogMapping.catalog')->whereNull('parent_id')->orderBy('updated_at','desc');
      if($searchTerm) {
          $package = $package->where('name', 'like', '%'.$searchTerm.'%');
      }
        if ($request->has('lab_id') && $request->lab_id != '') {
            $package = $package->where('lab_id', $request->lab_id);
        } 
        if ($request->has('is_featured') && $request->is_featured != '') {
            $package = $package->where('is_featured', $request->is_featured);
        } 

        if ($maxPrice !== null) {
            $package = $package->whereBetween('price', [$minPrice, $maxPrice]);
        } else {
            $package = $package->where('price', '>=', $minPrice);
        }
      $package = $package->orderBy('id','desc')->paginate($perPage);
      

      $responseData = PackageResource::collection($package);

      return response()->json([
          'status' => true,
          'data' => $responseData,
          'message' => __('genres.genres_list'),
      ], 200);
  }
    public function packageDetail(Request $request)
    {
        $package = PackageManagement::find($request->id);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => __('messages.package_not_found'),
            ], 404);
        }

        $responseData = new PackageDetailResource($package);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.package_details_retrive'),
        ], 200);
    }
    public function testCaseListByPackage(Request $request)
    {
        $package = PackageManagement::find($request->id);

        if (!$package) {
            return response()->json([
                'status' => false,
                'message' => __('messages.package.not_found'),
            ], 404);
        }

        $responseData = CatlogResource::collection(CatlogManagement::whereIn('id', $package->packageCatlogMapping->pluck('catalog_id'))->get());

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.package_details_retrive'),
        ], 200);
    }
}
