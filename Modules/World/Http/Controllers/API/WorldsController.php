<?php

namespace Modules\World\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;

class WorldsController extends Controller
{
  // api controller logic
  public function getCountryList(Request $request)
  {
      $perPage = $request->input('per_page', 10);
      $searchTerm = $request->input('search', null);
     
      $list = Country::where('status', 1);
      if($searchTerm) {
          $list = $list->where('name', 'like', '%'.$searchTerm.'%');
      }
      $list = $list->paginate($perPage);
      return response()->json( $list );
  }

  public function getStateList(Request $request)
  {
    $perPage = $request->input('per_page', 10);
    $searchTerm = $request->input('search', null);
    $list = State::where('country_id',$request->country_id)->where('status', 1);
    if($searchTerm) {
        $list = $list->where('name', 'like', '%'.$searchTerm.'%');
    }
    $list = $list->paginate($perPage);

      return response()->json( $list );
  }

  public function getCityList(Request $request)
  {
    $perPage = $request->input('per_page', 10);
    $searchTerm = $request->input('search', null);
    $list = City::where('status', 1);
        if(!empty($request->state_id)){
            $list = City::where('state_id',$request->state_id);
        }
    if($searchTerm) {
        $list = $list->where('name', 'like', '%'.$searchTerm.'%');
    }
    $list = $list->paginate($perPage);
     

      return response()->json( $list );
  }
  
}
