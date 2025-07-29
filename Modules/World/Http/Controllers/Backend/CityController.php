<?php

namespace Modules\World\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Carbon\Carbon;
use Modules\World\Models\City;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\World\Http\Requests\CityRequest;
use App\Trait\ModuleTrait;
use Modules\World\Models\State;

class CityController extends Controller
{
    use ActivityLogger;

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.city', // module title
            'city', // module name
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

        $states = State::select('id', 'name')->get();

        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ]
        ];

        return view('world::backend.city.index', compact('module_action', 'filter', 'export_columns'));
    }
    public function index_list(Request $request)
    {
        $stateId = $request->state_id ?? null;
        if($stateId){
            $cities = City::where('state_id', $stateId)->where('status', 1)->get();
        }else{
            $cities = City::where('status', 1)->get();
        }
        return response()->json($cities);
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.city'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(City::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function update_status(Request $request, city $id)
    {

        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = city::query();

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }

        return $datatable->eloquent($query)
          ->editColumn('name', fn($data) => $data->name)
          ->editColumn('state_id', function ($data) {
            return $data->city->name ?? '-';
        })
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
          })
          ->addColumn('action', function ($data) {
              return view('world::backend.city.action', compact('data'));
          })
          ->editColumn('status', function ($row) {
            $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is true
            $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
        
            return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="' . route('backend.city.update_status', $row->id) . '" 
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
        })        
          ->editColumn('updated_at', fn($data) => $this->formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check'])
          ->orderColumns(['id'], '-:column $1')
          ->make(true);
    }

    private function formatUpdatedAt($updatedAt)
      {
          $diff = Carbon::now()->diffInHours($updatedAt);
          return $diff < 25 ? $updatedAt->diffForHumans() : $updatedAt->isoFormat('llll');
      }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

      public function create()
    {
        $states = State::select('id', 'name')->get();
        $module_title = __('messages.add');
      return view('world::backend.city.create', compact('states','module_title'));
    }

    public function store(CityRequest $request)
    {
        $data = $request->all();
        $city = city::create($data);
        $this->logActivity('create',$city,'city_create');
        return redirect()->route('backend.city.index', $city->id)->with('success', __('messages.record_add'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = city::find($id);
        $states = State::select('id', 'name')->get();
        $module_title = __('city.add_title');
    return view('world::backend.city.edit', compact('data','states','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CityRequest $request, city $city)
    {
        $requestData = $request->all();
        $city->update($requestData);
        $this->logActivity('create',$city,'city_update');
        return redirect()->route('backend.city.index', $city->id)->with('success',  __('messages.record_update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = city::find($id);
        $data->delete();
        $this->logActivity('delete',$data,'city_delete');
        $message = __('messages.record_deleted_successfully');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = city::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'city_restore');
        return response()->json(['message' => __('messages.city_restore')]);
    }

    public function forceDelete($id)
    {
        $data = city::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'city_force_delete');
        return response()->json(['message' => __('messages.city_force_delete')]);
    }

}
