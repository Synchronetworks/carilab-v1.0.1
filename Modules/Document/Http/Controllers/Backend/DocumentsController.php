<?php

namespace Modules\Document\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Document\Models\Document;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Document\Http\Requests\DocumentRequest;
use App\Trait\ModuleTrait;

class DocumentsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\DocumentExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.lbl_document', // module title
            'documents', // module name
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
        $type = 'document';
        $module_action = __('messages.list');
        $module_title = __('messages.documents');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ],
            [
                'value'=>'user_type',
                'text'=>__('messages.user_type'),
            ],
            [
                'value'=>'is_required',
                'text'=> __('messages.required'),
            ],
            [
                'value'=>'status',
                'text'=>__('messages.lbl_status'),
            ]
        ];
        $export_url = route('backend.documents.export');

        return view('document::backend.document.index', compact('module_action','module_title', 'filter', 'export_import', 'export_columns', 'export_url','type'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.lbl_document'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Document::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Document::query()->withTrashed();

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)
          ->editColumn('name', fn($data) => $data->name)
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="document" onclick="dataTableRowCheck('.$data->id.',this)">';
          })
          ->editColumn('user_type', fn($data) => ucfirst($data->user_type))
          ->addColumn('action', function ($data) {
              return view('document::backend.document.action', compact('data'));
          })

          ->editColumn('status', function ($row) {
            $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is true
            $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
        
            return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="' . route('backend.documents.update_status', $row->id) . '" 
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
        }) 
        ->editColumn('is_required', function ($row) {
            $checked = $row->is_required ? 'checked="checked"' : ''; // Check if the status is true
            $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
        
            return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="' . route('backend.documents.update_required', $row->id) . '" 
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
        }) 
       
        
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check','status','is_required'])
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
        $id = $request->id;
        $auth_user = authSession();

        $documentdata = Document::find($id);
        $pageTitle = trans('messages.update_form_title',['form'=>trans('messages.document')]);
        
        if( $documentdata == null){
            $pageTitle = trans('messages.add_button_form',['form' => trans('messages.document')]);
             $documentdata = new Document;
        }
        $module_title=__('messages.new_document');
      return view('document::backend.document.create',compact('pageTitle' ,'documentdata' ,'auth_user','module_title' ));
    }

    public function store(DocumentRequest $request)
    {
    
        $data = $request->all();

        if(!$request->is('api/*')) {
            $data['is_required'] = 0;
            if($request->has('is_required')){
                $data['is_required'] = 1;
            }
        }
        $result = Document::updateOrCreate(['id' => $data['id'] ],$data);
        $this->logActivity('create',$result,'document_create');
        $message = trans('messages.update_form',['form' => trans('messages.document')]);
        if($result->wasRecentlyCreated){
            $message = trans('messages.save_form',['form' => trans('messages.document')]);
        }
        if($request->is('api/*')) {
            return comman_message_response($message);
		}
        return redirect()->route('backend.documents.index')->with('success', $message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $documentdata = Document::findOrFail($id);
        $module_title=__('messages.edit_document');
    return view('document::backend.document.edit', compact('documentdata','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(DocumentRequest $request, Document $document)
    {
        $requestData = $request->all();
        $document->update($requestData);
        $this->logActivity('update',$document,'document_update');
        $message=__('messages.update_form');
        return redirect()->route('backend.documents.index', $document->id)->with('success',$message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Document::findOrFail($id);
        $data->delete();
        $this->logActivity('delete',$data,'document_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Document::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'document_restore');
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Document::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'document_force_delete');
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }

    public function update_status(Request $request, Document $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function update_required(Request $request, Document $id)
    {
        
        $id->update(['is_required' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

}
