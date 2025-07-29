<?php

namespace Modules\NotificationTemplate\Http\Controllers\Backend;
use App\Models\User;
use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use Yajra\DataTables\DataTables;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplateContentMapping;

class NotificationTemplatesController extends Controller
{
    protected string $exportClass = '\App\Exports\NotificationTemplateExport';

    public function __construct()
    {
        $this->global_booking = false;
        // Page Title
        $this->module_title = 'notification.title_template';

        // module name
        $this->module_name = 'notificationtemplates';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'global_booking' => $this->global_booking,
        ]);
        $this->middleware(['permission:view_notification_template'])->only('index');
        $this->middleware(['permission:edit_notification_template'])->only('edit', 'update');
        $this->middleware(['permission:add_notification_template'])->only('store');
        $this->middleware(['permission:delete_notification_template'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = __('messages.list');

        $filter = [
            'status' => request()->status,
        ];

        $export_import = true;
        $export_columns = [
            [
                'value' => 'id',
                'text' => __('messages.id'),
            ],
            [
                'value' => 'label',
                'text' => __('messages.lbl_label'),
            ],

        ];
        $export_url = route('backend.notificationtemplates.export');
        return view('notificationtemplate::backend.notificationtemplates.index_datatable', compact('module_action','filter', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $service_providers = NotificationTemplate::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_notification_update');
                break;

            case 'delete':
                NotificationTemplate::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_notification_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function index_list(Request $request)
    {
        $query_data = NotificationTemplate::with('defaultNotificationTemplateMap', 'constant')->get();

        $data = [];

        $notificationKeyChannels = array_keys(config('notificationtemplate.channels'));

        $arr = [];
        // For Channel Map Or Update Channel Value
        foreach ($notificationKeyChannels as $key => $value) {
            $arr[$value] = 0;
        }

        foreach ($query_data as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'type' => $value->type,
                'template' => $value->defaultNotificationTemplateMap->subject,
                'is_default' => false,
            ];

            if (isset($value->channels)) {
                $data[$key]['channels'] = $value->channels;
            } else {
                $data[$key]['channels'] = $arr;
            }
        }

        $notificationChannels = config('notificationtemplate.channels');

        return response()->json(['data' => $data, 'channels' => $notificationChannels, 'status' => true, 'message' => __('messages.notification_temp_list')]);
    }

    public function update_status(Request $request, NotificationTemplate $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function index_data(Datatables $datatable)
    {
        $query = NotificationTemplate::query()->with('defaultNotificationTemplateMap');

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($data) {
                return view('notificationtemplate::backend.notificationtemplates.action_column', compact('data'));
            })
            ->editColumn('label', function ($row) {
                return '<a href="'.route('backend.notification-templates.edit', $row->id).'">'.optional($row->defaultNotificationTemplateMap)->subject.'</a>';
            })
            ->filterColumn('label', function ($query, $keyword) {
                $query->whereHas('defaultNotificationTemplateMap', function ($q) use ($keyword) {
                    $q->where('subject', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('label', function ($query, $direction) {
                $query->orderBy(DB::raw('(SELECT subject FROM notification_template_content_mapping 
                    WHERE template_id = notification_templates.id 
                    AND language = "en" 
                    LIMIT 1)'), $direction);
            })       
            ->editColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is active
                $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
            
                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.notificationtemplates.update_status', $row->id) . '" 
                               data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                               id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                               ' . $checked . ' ' . $disabled . '>
                    </div>
                ';
            })            
            ->editColumn('updated_at', function ($data) {
                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');


        return $datatable->rawColumns(array_merge(['label', 'action', 'status', 'check'], ))
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $module_action = __('messages.create');

        $assets = ['textarea'];

        return view('notificationtemplate::backend.notificationtemplates.create', compact('module_action', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $map = $request->defaultNotificationTemplateMap;
        $request->merge(['type' => $request->type]);

        $map['subject'] = $request->defaultNotificationTemplateMap['subject'];
        $map['notification_message'] = $request->defaultNotificationTemplateMap['notification_message'];
        $map['notification_link'] = $request->defaultNotificationTemplateMap['notification_link'];

        $request['to'] = isset($request->to) ? json_encode($request->to) : null;
        $request['bcc'] = isset($request->bcc) ? json_encode($request->bcc) : null;
        $request['cc'] = isset($request->cc) ? json_encode($request->cc) : null;

        $data = NotificationTemplate::create($request->all());
        $data->defaultNotificationTemplateMap()->create($map);

        $message = trans('messages.create_form');

        return redirect()->route('backend.notification-templates.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_name = $this->module_name;

        $module_name_singular = Str::singular($module_name);

        $module_action = __('messages.show');

        $data = NotificationTemplate::findOrFail($id);

        return view('notificationtemplate::backend.notificationtemplates.show', compact('module_name_singular', 'module_action', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $module_action = __('messages.edit');
        $data = NotificationTemplate::with('defaultNotificationTemplateMap', 'constant')->findOrFail($id);
        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($data) {
                $query->where('sub_type', $data->type)->orWhere('sub_type', null);
            })->get();

        
        $assets = ['textarea'];

        return view('notificationtemplate::backend.notificationtemplates.edit', compact('module_action', 'data', 'assets', 'buttonTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $map = $request->defaultNotificationTemplateMap;
        $type = $request->type;
            // Handle Notification Template
            if ($request->has('defaultNotificationTemplateMap')) {
                $userType = $request->defaultNotificationTemplateMap['user_type'];
                $ids = NotificationTemplate::where('type', $type)->pluck('id');
    
                $check = NotificationTemplateContentMapping::with('template')
                    ->whereIn('template_id', $ids)
                    ->where('user_type', $userType)
                    ->first();
    
                if ($check !== null) {
                    $data = NotificationTemplateContentMapping::find($check->id);
                    if ($data !== null) {
                        $map = $request->defaultNotificationTemplateMap;
                        $data->update([
                            'subject' => $map['subject'],
                            'template_detail' => $map['template_detail'],
                            'mail_subject' => $map['mail_subject'],
                            'mail_template_detail' => $map['mail_template_detail'],
                            'sms_subject' => $map['sms_subject'],
                            'sms_template_detail' => $map['sms_template_detail'],
                            'whatsapp_subject' => $map['whatsapp_subject'],
                            'whatsapp_template_detail' => $map['whatsapp_template_detail'],
    
                        ]);
                        $data->template->update([
                            'to' => isset($request->to) ? json_encode($request->to) : null,
                            'status' => $request->has('status') ? 1 : 0,
                        ]);
    
                        $notificationMessage = __('messages.notification_template_updated');
                    } else {
                        $notificationMessage = __('messages.notification_template_not_found');
                    }
                } else {
                    $data = NotificationTemplate::updateOrCreate(['type' => $type], [
                        'name' => $type,
                        'description' => $request->description,
                        'to' => isset($request->to) ? json_encode($request->to) : null,
                        'status' => $request->has('status') ? 1 : 0,
                    ]);
    
                    $data->defaultNotificationTemplateMap()->create($request->defaultNotificationTemplateMap);
    
                    $notificationMessage = __('messages.notification_template_created');
                }
            }
        $message = __('messages.update_form');

        flash("<i class='fas fa-check'></i> $message")->success()->important();

        return redirect()->route('backend.notification-templates.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = NotificationTemplate::findOrFail($id);
        $data->delete();

        $message = __('messages.delete_form');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = __('messages.trash_list');

        $data = NotificationTemplate::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('notificationtemplate::backend.notificationtemplates.trash', compact('data', 'module_name_singular', 'module_action'));
    }

    /**
     * Restore a soft deleted entry.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function restore($id)
    {
        $module_name = $this->module_name;
        $module_name_singular = Str::singular($module_name);
        $$module_name_singular = NotificationTemplate::withTrashed()->find($id);
        $$module_name_singular->restore();

        flash('<i class="fas fa-check"></i> '.label_case($module_name_singular).__('messages.data_restore'))->success()->important();

        return redirect("app/$module_name");
    }

    public function getAjaxList(Request $request)
    {
        $items = [];
        $value = $request->q;
        switch ($request->type) {
            case 'constants':
                $items = Constant::select(\DB::raw('id,name text'))
                    ->where(function ($query) use ($value) {
                        $query->where(\DB::raw('value', 'LIKE', '%'.$value.'%'));
                        $query->orWhere('value', 'LIKE', '%'.$value.'%');
                    })
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('sequence', 'ASC')
                    ->where('type', $request->data_type);
                $items = $items->get();
                break;
            case 'constants_key':
                $items = DB::table('constants')->select(DB::raw('value id, name text'))
                    ->where(function ($query) use ($value) {
                        $query->where(DB::raw('value', 'LIKE', '%'.$value.'%'));
                        $query->orWhere('value', 'LIKE', '%'.$value.'%');
                    })
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('sequence', 'ASC')
                    ->where('type', $request->data_type);
                $items = $items->get();
                if ($request->data_type === 'notification_to') {
                    $items = $items->map(function ($item) {
                        if ($item->id === 'user') {
                            $item->text = 'customer';
                        }
                        return $item;
                    });
                }
                break;
                break;
            default:
                break;
        }

        return response()->json(['status' => 'true', 'results' => $items]);
    }

    public function notificationButton(Request $request)
    {
        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($request) {
                $query->where('sub_type', $request->type)->orWhere('sub_type', null);
            })->get();

        return view('notificationtemplate::backend.notificationtemplates.perameters-buttons', compact('buttonTypes'));
    }

    public function notificationTemplate(Request $request)
    {
        $detail = NotificationTemplateContentMapping::where(['template_id' => $request->template_id, 'mailable_id' => $request->mailable_id, 'language' => $request->language])->first();
        if (! isset($type)) {
            $detail = NotificationTemplate::find($request->template_id);
        }

        return response()->json(['data' => $detail, 'status' => true]);
    }

    public function updateChanels(Request $request)
    {
        $data = $request->except('selected_session_service_provider_id');
        $data=$data['templates'];

        foreach ($data as $key => $value) {

            if (isset($value['id'])) {
                $notificationTemplate = NotificationTemplate::find($value['id']);

                $notificationTemplate->channels = $value['channels'] ?? '';

                $notificationTemplate->save();
            }
        }

        $message = __('messages.notification_setting_updated');

        return redirect()->back()->with('success', $message);
    }

    public function fetchNotificationData(Request $request)
    {
        $userType = $request->input('user_type');
        $type = $request->input('type');

        $ids = NotificationTemplate::where('type', $type)->pluck('id');

        $data = NotificationTemplateContentMapping::with('template')->whereIn('template_id', $ids)
            ->where('user_type', $userType)
            ->first();

        $notification_templte_ids = NotificationTemplate::where('type', $type)->pluck('id') ;
        $notification_template_data = NotificationTemplateContentMapping::with('template')->whereIn('template_id', $notification_templte_ids)
            ->where('user_type', $userType)
            ->first();
    

        if ($data) {
            return response()->json(['success' => true, 'data' => $data,'notification_template_data'=>$notification_template_data]);
        } else {
            return response()->json(['success' => false, 'message' => __('messages.no_data_found_select_usertype')]);
        }
    }
}
