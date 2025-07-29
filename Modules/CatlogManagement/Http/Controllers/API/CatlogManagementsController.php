<?php

namespace Modules\CatlogManagement\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\CatlogManagement\Transformers\CatlogResource;
use Modules\CatlogManagement\Transformers\CatlogDetailResource;

class CatlogManagementsController extends Controller
{

  public function catlogList(Request $request)
  {
      $perPage = $request->input('per_page', 10);
      $searchTerm = $request->input('search', null);
     
      $testcases = CatlogManagement::where('status', 1)->whereNull('parent_id')->orderBy('updated_at','desc');
      
      if ($request->has('category_ids') && $request->category_ids != '') {
        $categoryIds = explode(',', $request->category_ids);
        $testcases = $testcases->whereIn('category_id', $categoryIds);
    } 
    
    if ($request->has('min_price') && $request->has('max_price')) {
        $testcases = $testcases->whereBetween('price', [$request->min_price, $request->max_price]);
    } elseif ($request->has('min_price')) {
        $testcases = $testcases->where('price', '>=', $request->min_price);
    } elseif ($request->has('max_price')) {
        $testcases = $testcases->where('price', '<=', $request->max_price);
    }
    if ($request->has('lab_id') && $request->lab_id != '') {
        $testcases = $testcases->where('lab_id', $request->lab_id);
    } 
    if ($request->has('parent_id')) {
        $testcases = CatlogManagement::where('status', 1)->where('parent_id', $request->parent_id);
    }
    if ($request->has('is_featured') && $request->is_featured != '') {
        $testcases = $testcases->where('is_featured', $request->is_featured);
    } 

      if($searchTerm) {
          $testcases = $testcases->where('name', 'like', '%'.$searchTerm.'%');
      }
      $testcases = $testcases->paginate($perPage);

      $responseData = CatlogResource::collection($testcases);

      return response()->json([
          'status' => true,
          'data' => $responseData,
          'message' => __('messages.test_case_list'),
      ], 200);
  }
  public function getCatlogDetail(Request $request)
    {
        $catlog = CatlogManagement::with('packageCatlogMapping.package')->find($request->id);

        if (!$catlog) {
            return response()->json([
                'status' => false,
                'message' => __('messages.catlog_not_found'),
            ], 404);
        }

        $responseData = new CatlogDetailResource($catlog);
       
        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.catlog_details_retrived'),
        ], 200);
    }
}
