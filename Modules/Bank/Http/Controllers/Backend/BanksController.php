<?php

namespace Modules\Bank\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\Bank\Models\Bank;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Bank\Http\Requests\BankRequest;
use App\Trait\ModuleTrait;

class BanksController extends Controller
{
    protected string $exportClass = '\App\Exports\BankExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.banks', // module title
            'banks', // module name
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

        $type = 'bank';
        if ($user_type === 'vendor') {
            $module_title = __('messages.vendor_banks');
        } elseif ($user_type === 'collector') {
            $module_title = __('messages.collector_banks');
        }

        $module_action = __('messages.list');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'bank_name',
                'text' => __('messages.lbl_bank_name'),
            ],
            [
                'value' => 'branch_name',
                'text' => __('messages.lbl_branch_name'),
            ],
            [
                'value' => 'user_name',
                'text' => $user_type === 'vendor' ? __('messages.lbl_vendorName') : __('messages.lbl_collector_name'),            ],
            [
                'value' => 'phone_number',
                'text' => __('messages.lbl_conatct_number'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
        $permissions = [
            'edit' => $user_type === 'collector' ? 'edit_collector_bank' : 'edit_vendor_bank',
            'delete' => $user_type === 'collector' ? 'delete_collector_bank' : 'delete_vendor_bank',
            'restore' => $user_type === 'collector' ? 'restore_collector_bank' : 'restore_vendor_bank',
            'forceDelete' => $user_type === 'collector' ? 'force_delete_collector_bank' : 'force_delete_vendor_bank',
        ];
        $export_url = route('backend.banks.export', ['user_type' => $user_type]);

        return view('bank::backend.bank.index', compact('module_action', 'permissions','filter', 'export_import', 'export_columns', 'export_url','user_type','module_title','type'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.banks'); 
        $messageKey = __('messages.bulk_action');

        return $this->performBulkAction(Bank::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Bank::query();

        $filter = $request->filter;
        if (!empty($filter['user_type'])) {
            $query->MyBank($filter['user_type'])->where('user_type', $filter['user_type']);
        }

        if (isset($filter['bank_name'])) {
            $query->where('bank_name', $filter['bank_name']);
        }
        if (isset($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        }
        if (isset($filter['user_name'])) {
            $query->whereHas('user', function ($q) use ($filter) {
                $searchTerm = strtolower($filter['user_name']);
                $q->whereRaw("LOWER(first_name) LIKE ?", ['%' . $searchTerm . '%'])
                  ->orWhereRaw("LOWER(last_name) LIKE ?", ['%' . $searchTerm . '%'])
                  ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ['%' . $searchTerm . '%']);
            })->orWhereNull('user_id');
        }

        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }


        return $datatable->eloquent($query)
          ->editColumn('bank_name', fn($data) => $data->bank_name)
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="bank" onclick="dataTableRowCheck('.$data->id.',this)">';
          })
          


            ->addColumn('action', function ($data) {
                return view('bank::backend.bank.action', compact('data'));
            })

            ->addColumn('user_name', function ($data) {
                $data = $data->user ? $data->user : null;
                if ($data != null) {
                    return view('user::backend.users.user_details', compact('data'));
                }
                return '-';
            })
            ->addColumn('phone_number', function ($data) {
                return $data->user ? $data->user->mobile : '-';
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

    public function create(Request $request)
    {
        $user_type = $request->route('user_type');
        $users = User::where('user_type', $user_type)->get();
        $module_action = __('messages.add');
        if ($user_type === 'vendor') {
            $module_title = __('messages.vendor_banks');
        } elseif ($user_type === 'collector') {
            $module_title = __('messages.collector_banks');
        }
        return view('bank::backend.bank.create', compact('user_type', 'users', 'module_title', 'module_action'));
    }

    public function store(BankRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->hasAnyRole(['collector', 'vendor']) ? auth()->id() : $request->user_id;
        $data['user_type'] = User::where('id', $data['user_id'])->first()->user_type;

        
        if (!empty($data['is_default']) && $data['is_default'] == 1) {
            Bank::where('user_id', $data['user_id'])->update(['is_default' => 0]);
        }

        $bank = Bank::create($data);
        $message = __('messages.record_add');

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $bank, 'status' => true], 200);
        }
        if(auth()->user()->hasRole('vendor') && $data['user_type'] == 'vendor'){
            return redirect()->route('backend.vendors.details', ['id' => auth()->id()])->with('success', $message);
        }
        return redirect()->route('backend.' . $data['user_type'] . '_bank.index')->with('success', $message);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $data = Bank::findOrFail($id);
    
        if ($user->hasRole('vendor')) {
            if ($data->user_type === 'vendor') {
                $bank = Bank::where('id', $id)->where('user_id', $user->id)->first();
            } elseif ($data->user_type === 'collector') {
                $bank = Bank::where('id', $id)
                    ->whereHas('user.collectorVendormapping', function ($qry) use ($user) {
                        $qry->where('vendor_id', $user->id);
                    })
                    ->first();
            }
    
            if (!$bank) {
                return redirect()
                    ->route($data->user_type == 'vendor' ? 'backend.vendors.details' : 'backend.collector_bank.index', ['id' => $user->id])
                    ->with('error', __('messages.record_not_found'));
            }
        }
        
       
        $users = User::where('user_type', $data->user_type);
        $module_action = __('messages.edit');
        if ($data->user_type === 'vendor') {
            $users = $users->MyVendor()->get();
            $module_title = __('messages.vendor_banks');
        } elseif ($data->user_type === 'collector') {
            $users = $users->myCollector()->get();
            $module_title = __('messages.collector_banks');
        }

        return view('bank::backend.bank.edit', compact('data', 'users', 'module_title', 'module_action'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(BankRequest $request, $id)
    {
        $bank = Bank::findOrFail($id);
        if ($bank == null) {
            return redirect()->route('backend.collector_bank.index')->with('error',  __('messages.record_not_found'));
        }

        $requestData = $request->all();
        $user_type = $bank->user_type;

        // If is_default is 1, update all other banks for this user to is_default = 0
        if (!empty($requestData['is_default']) && $requestData['is_default'] == 1) {
            Bank::where('user_id', $bank->user_id)->where('id', '!=', $id)->update(['is_default' => 0]);
        }

        $bank->update($requestData);
        $message = __('messages.record_update');

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $bank, 'status' => true], 200);
        }
        if(auth()->user()->hasRole('vendor') && $user_type == 'vendor'){
            return redirect()->route('backend.vendors.details', ['id' => auth()->id()])->with('success', $message);
        }
        return redirect()->route('backend.' . $user_type . '_bank.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Bank::findOrFail($id);
        $data->delete();
        $message = __('messages.delete_form');
        return response()->json(['message' => $message, 'type' => 'DELETE_FORM', 'status' => true], 200);
    }

    public function restore($id)
    {
        $data = Bank::withTrashed()->findOrFail($id);
        $data->restore();
        $message = __('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Bank::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }
}
