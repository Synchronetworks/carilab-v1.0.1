<?php

namespace Modules\Wallet\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Wallet\Models\Wallet;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Wallet\Http\Requests\WalletRequest;
use App\Trait\ModuleTrait;

class WalletsController extends Controller
{
    protected string $exportClass = '\App\Exports\WalletExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.wallet', // module title
            'wallets', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = __('messages.list');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ]
        ];
        $export_url = route('backend.wallets.export');

        return view('wallet::backend.wallet.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.wallet'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Wallet::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Wallet::query()->withTrashed();

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }

        return $datatable->eloquent($query)
          ->editColumn('name', fn($data) => $data->name)
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
          })
          ->addColumn('action', function ($data) {
              return view('wallet::backend.wallet.action', compact('data'));
          })
          ->editColumn('status', function ($data) {
              return $data->getStatusLabelAttribute();
          })
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check'])
          ->orderColumns(['id'], '-:column $1')
          ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

      public function create()
    {
      return view('wallet::backend.wallet.create');
    }

    public function store(WalletRequest $request)
    {
        $data = $request->all();
        $data = $request->all();
        $data['user_id'] = !empty($request->user_id) ? $request->user_id : auth()->id();
        $wallet = Wallet::where('user_id',$data['user_id'])->first();
        if($wallet && !$data['id']){
            $message = __('messages.already_wallet');
            return redirect()->back()->withError($message);
        }
        if($wallet !== null){
            $data['amount'] = $wallet->amount + $request->amount;
        }
        $result = Wallet::updateOrCreate(['id' => $data['id'] ],$data);


        $message = trans('messages.update_form');
        if($result->wasRecentlyCreated){
            $activity_data = [
                'activity_type' => 'add_wallet',
                'wallet' => $result,
            ];
           

            $message = trans('messages.save_form');
        }else{
            if($wallet->amount  != $data['amount']){
                $activity_data = [
                    'activity_type' => 'update_wallet',
                    'wallet' => $result,
                    'added_amount' =>$request->amount
                ];
                

            }
        }
        if($request->is('api/*')) {
            $status_code = 200;
            return comman_custom_response($message, $status_code);
		}

        return redirect()->route('backend.wallets.index')->with('success', __('messages.record_add'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Wallet::findOrFail($id);
    return view('wallet::backend.wallet.edit', compact('data'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(WalletRequest $request, Wallet $wallet)
    {
        $requestData = $request->all();
        $wallet->update($requestData);

        return redirect()->route('backend.wallets.index', $wallet->id)->with('success', __('messages.wallet_update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Wallet::findOrFail($id);
        $data->delete();
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Wallet::withTrashed()->findOrFail($id);
        $data->restore();
        return response()->json(['message' => __('messages.restore_form')]);
    }

    public function forceDelete($id)
    {
        $data = Wallet::withTrashed()->findOrFail($id);
        $data->forceDelete();
        return response()->json(['message' => __('messages.permanent_delete_form')]);
    }
}
