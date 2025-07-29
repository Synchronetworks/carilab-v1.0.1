<?php

namespace Modules\Helpdesk\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Modules\Helpdesk\Models\Helpdesk;
use Illuminate\Http\Request;
use Modules\Helpdesk\Models\Helpdeskactivity;
use Modules\Helpdesk\Transformers\HelpDeskActivityResource;
use Modules\Helpdesk\Transformers\HelpDeskResource;

class HelpdesksController extends Controller
{
  public function getHelpDeskList(Request $request){  
    $auth_user = auth()->user();
    $helpdesk = Helpdesk::orderBy('id','desc')->where('user_id',$auth_user->id);
   
    if(auth()->user() !== null){
        if(auth()->user()->hasRole('admin')){
            $helpdesk = new HelpDesk();
            $helpdesk = $helpdesk->withTrashed();
        }
    }
    if ($request->has('status') && isset($request->status)) {
        if($request->status == 'open'){
            $helpdesk = $helpdesk->where('status', 0);
        }else if($request->status == 'closed'){
            $helpdesk = $helpdesk->where('status', 1);
        }else{
            $helpdesk = $helpdesk;
        }
    }
    $per_page = config('constant.PER_PAGE_LIMIT');
    if( $request->has('per_page') && !empty($request->per_page)){
        if(is_numeric($request->per_page)){
            $per_page = $request->per_page;
        }
        if($request->per_page === 'all' ){
            $per_page = $helpdesk->count();
        }
    }

    $helpdesk = $helpdesk->orderBy('id','desc')->paginate($per_page);
    $items = HelpDeskResource::collection($helpdesk);

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


public function getHelpDeskDetail(Request $request){  
  
    $helpdeskactivity = Helpdeskactivity::with('HelpDesk')->where('helpdesk_id',$request->id);
   
    $per_page = config('constant.PER_PAGE_LIMIT');
    if( $request->has('per_page') && !empty($request->per_page)){
        if(is_numeric($request->per_page)){
            $per_page = $request->per_page;
        }
        if($request->per_page === 'all' ){
            $per_page = $helpdeskactivity->count();
        }
    }
    $status = optional($helpdeskactivity->first()?->HelpDesk)->status == '0' ? __('messages.open') : __('messages.closed');
    $helpdeskactivity = $helpdeskactivity->paginate($per_page);

    
    $items = HelpDeskActivityResource::collection($helpdeskactivity);

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
        'status' => $status,
        'activity' => $items,
    ];
    
    return comman_custom_response($response);
}
}
