<?php

namespace Modules\Commision\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Commision\Models\Commision;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Commision\Http\Requests\CommisionRequest;
use App\Trait\ModuleTrait;
use Illuminate\Support\Facades\DB;

class CommisionsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\CommisionExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'commision.title', // module title
            'commisions', // module name
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
        $user_type = $request->route('user_type');
        $filter = [
            'status' => $request->status,
        ];
        $type = 'commision';
        $module_action = __('messages.list');
      
        if( $user_type=='collector'){

        $moduleName=__('messages.collector_commisions');
        }else{
            $moduleName=__('messages.vendor_commisions');
        }

        $permissions = [
            'edit' => $user_type === 'collector' ? 'edit_collector_commisions' : 'edit_vendor_commisions',
            'delete' => $user_type === 'collector' ? 'delete_collector_commisions' : 'delete_vendor_commisions',
            'restore' =>$user_type === 'collector' ? 'restore_collector_commisions' : 'restore_vendor_commisions',
            'forceDelete' => $user_type === 'collector' ? 'force_delete_collector_commisions' : 'force_delete_vendor_commisions',
        ];
        $export_import = true;
        $export_columns = [
            [
                'value' => 'title',
                'text' => __('messages.lbl_title'),
            ],
            [
                'value' => 'type',
                'text' => __('messages.lbl_Type'),
            ],
            [
                'value' => 'value_colmun',
                'text' => __('messages.lbl_value'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
        $export_url = route('backend.commisions.export',['user_type' => $user_type]);

        return view('commision::backend.commision.index', compact('module_action','permissions', 'filter', 'export_import', 'export_columns', 'export_url','user_type','moduleName','type'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'Commision'; // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Commision::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Commision::query()->withTrashed();

        $filter = $request->filter;
        if (!empty($filter['user_type'])) {
            $query->where('user_type', $filter['user_type']);
        }

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }

        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)
          ->editColumn('name', fn($data) => $data->name)
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="commision" onclick="dataTableRowCheck('.$data->id.',this)">';
          })
          ->addColumn('action', function ($data) {
              return view('commision::backend.commision.action', compact('data'));
          })
          ->editColumn('status', function ($row) {
            $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is true
            $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
        
            return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="' . route('backend.commisions.update_status', $row->id) . '" 
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
        })        
        ->editColumn('value', function ($row) {
            if ($row->type === 'Fixed') {
                return \Currency::format($row->value);  
            } else if ($row->type === 'Percentage') {
                return $row->value.'%';  
            }
            return $row->value;  
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

      public function create(Request $request)
    {
      $user_type = $request->route('user_type');
      if( $user_type=='collector'){
            $module_title=__('messages.new_collector_commision');
        }else{
            $module_title=__('messages.new_vendor_commision');
        }
    
      return view('commision::backend.commision.create',compact('user_type','module_title'));
    }

    public function store(CommisionRequest $request)
    {
        $data = $request->all();
        $user_type = $data['user_type'];
        $commision = Commision::create($data);
        $this->logActivity('create',$commision,'commision_create');
        $message=__('messages.record_add');
        return redirect()->route('backend.'. $user_type .'_commisions.index')->with('success', $message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Commision::findOrFail($id);
       
         if( $data->user_type=='collector'){
            $module_title=__('messages.edit_collector_commision');
        }else{
            $module_title=__('messages.edit_vendor_commision');
        }
    return view('commision::backend.commision.edit', compact('data','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CommisionRequest $request, Commision $commision)
    {
        \DB::beginTransaction();
        try {
            $requestData = $request->all();
            $commision->update($requestData);
            $user_type = $requestData['user_type'];
            
            
            if ($commision->deleted_at) {

                \App\Models\UserCommissionMapping::where('commission_id', $commision->id)
                    ->update([
                        'commission_type' => null,
                        'commission' => null
                    ]);
            } else {

                \App\Models\UserCommissionMapping::where('commission_id', $commision->id)
                    ->update([
                        'commission_type' => $requestData['type'],
                        'commission' => $requestData['value']
                    ]);
            }
            
            $this->logActivity('update', $commision, 'commision_update');
            
            \DB::commit();
            $message = __('messages.record_update');
            return redirect()->route('backend.'. $user_type .'_commisions.index')->with('success', $message);
            
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()->with('error', __('messages.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        \DB::beginTransaction();
        try {
            $data = Commision::findOrFail($id);
            
         
            \App\Models\UserCommissionMapping::where('commission_id', $id)
                ->update([
                    'commission_type' => null,
                    'commission' => null
                ]);
            
            // Delete the commission
            $data->delete();
            
            $this->logActivity('delete', $data, 'commision_delete');
            
            \DB::commit();
            
            $message = __('messages.delete_form');
            return response()->json([
                'message' => $message, 
                'type' => 'DELETE_FORM',
                'status' => true
            ], 200);
            
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'message' => __('messages.delete_failed'),
                'status' => false
            ], 500);
        }
    }

    public function restore($id)
    {
        $data = Commision::withTrashed()->findOrFail($id);
        $data->restore();
               
                \App\Models\UserCommissionMapping::where('commission_id', $id)
                ->update([
                    'commission_type' => $data->type,
                    'commission' => $data->value
                ]);
        $this->logActivity('restore',$data,'commision_restore');
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Commision::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'commision_force_delete');
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }

    public function update_status(Request $request, Commision $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }


}
