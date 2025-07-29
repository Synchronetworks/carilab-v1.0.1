<?php

namespace Modules\Payout\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payout\Models\Payout;
use Modules\Payout\Transformers\PayoutResource;
class PayoutsController extends Controller
{
    public function collectorPayoutList(Request $request){
      $collector_id  = !empty($request->collector_id) ? $request->collector_id : auth()->id();

      $payout = Payout::where('user_id',$collector_id);
      
      $per_page = config('constant.PER_PAGE_LIMIT');
      if( $request->has('per_page') && !empty($request->per_page)){
          if(is_numeric($request->per_page)){
              $per_page = $request->per_page;
          }
          if($request->per_page === 'all' ){
              $per_page = $payout->count();
          }
      }

      $payout = $payout->orderBy('id','desc')->paginate($per_page);
      $items = PayoutResource::collection($payout);

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
}
