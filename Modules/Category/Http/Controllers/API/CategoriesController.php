<?php

namespace Modules\Category\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Category\Models\Category;
use Modules\Category\Transformers\CategoryResource;
class CategoriesController extends Controller
{
  // api controller logic
  public function categoryList(Request $request)
  {
      $perPage = $request->input('per_page', 10);
      $searchTerm = $request->input('search', null);
     
      $category = Category::where('status', 1);
      if($searchTerm) {
          $category = $category->where('name', 'like', '%'.$searchTerm.'%');
      }
      $category = $category->orderBy('id','desc')->paginate($perPage);

      $responseData = CategoryResource::collection($category);

      return response()->json([
          'status' => true,
          'data' => $responseData,
          'message' => __('messages.category_list'),
      ], 200);
  }
}
