<?php

namespace Modules\Bank\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Bank\Models\Bank;
use Modules\Bank\Transformers\BankResource;

class BanksController extends Controller
{
  public function bankList(Request $request)
   {
       $perPage = $request->input('per_page', 10);
       $searchTerm = $request->input('search', null);
       $currentDate = now(); 
       $userId = $request->user_id ?? auth()->id();

       $bank = Bank::where('status', 1)->with('user');
   
     
       if (!empty($searchTerm)) {
           $bank->Where('bank_name', 'LIKE', "%{$searchTerm}%")->orWhere('branch_name', 'LIKE', "%{$searchTerm}%");
       }
       if ($userId ) {
            $userId = $request->input('user_id');
            $bank->where('user_id', $userId);
        }
      
   
       $banklist = $bank->paginate($perPage);
   
  
       $responseData = BankResource::collection($banklist);
   
       return response()->json([
           'status'  => true,
           'data'    => $responseData,
           'message' => __('messages.bank_list'),
       ], 200);
   }
}
