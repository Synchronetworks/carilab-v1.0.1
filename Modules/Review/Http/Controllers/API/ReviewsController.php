<?php

namespace Modules\Review\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Review\Models\Review;
use Modules\Review\Transformers\ReviewResource;
use App\Models\User;
class ReviewsController extends Controller
{
  // api controller logic
  public function reviewList(Request $request)
  {
      $perPage = $request->input('per_page', 10);
      $searchTerm = $request->input('search', null);
     
      $review = Review::where('status', 1);
      
      if($request->input('user_id')) {
          $review = $review->where('user_id', $request->input('user_id'));
      }
    if($request->input('lab_id')) {
        $review = $review->where('lab_id', $request->input('lab_id'));
    }
    if($request->input('collector_id')) {
        $review = $review->where('collector_id', $request->input('collector_id'));
    }
    if ($request->type == 'lab') {
        $review->whereNotNull('lab_id');
    }
    if ($request->type == 'collector') {
        $review->whereNotNull('collector_id');
    }
    if ($request->input('rating')) {
        $ratings = explode(',', $request->input('rating'));
        $review = $review->whereIn('rating', $ratings);
    }
    if($searchTerm) {
        $review = $review->where('name', 'like', '%'.$searchTerm.'%');
    }
      $review = $review->paginate($perPage);

      $responseData = ReviewResource::collection($review);

      return response()->json([
          'status' => true,
          'data' => $responseData,
          'message' => __('messages.review_list'),
      ], 200);
  }

  public function addReview(Request $request)
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
            $reviewList = Review::find($data['id']);
            if (!$reviewList) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => __('messages.record_not_found'),
                ], 404);
            }
            $reviewList->update($data);
            $message = __('messages.review_update');
        } else {
            $reviewList = Review::create($data);
            $message = __('messages.review_add');
        }


        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $reviewList,
            'message' => $message,
        ], 200);
    }
   
    public function deleteReview($id)
    {
        
        // Retrieve the record by ID
        $review = Review::find($id);

        // Check if the record exists
        if (!$review) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.record_not_found'),
            ], 404);
        }

        // Delete the record
        $review->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => __('messages.record_deleted_successfully'),
        ], 200);
       
    }
}
