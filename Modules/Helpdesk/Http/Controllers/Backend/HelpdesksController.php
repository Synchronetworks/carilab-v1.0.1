<?php

namespace Modules\Helpdesk\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use App\Trait\NotificationTrait;
use Modules\Helpdesk\Models\Helpdesk;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Helpdesk\Http\Requests\HelpdeskRequest;
use App\Trait\ModuleTrait;
use App\Models\User;
use Modules\Helpdesk\Models\Helpdeskactivity;
use App\Models\Setting;
class HelpdesksController extends Controller
{
    use NotificationTrait;  
    use ActivityLogger;

    protected string $exportClass = '\App\Exports\HelpdeskExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.helpdesk', // module title
            'helpdesks', // module name
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
        $export_url = route('backend.helpdesks.export');

        return view('helpdesk::backend.helpdesk.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.helpdesk'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Helpdesk::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = Helpdesk::with('users');
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        } else {
            $query->where('user_id', auth()->id());
        }
        
        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-'.$row->id.'" name="datatable_ids[]" value="'.$row->id.'" data-type="helpdesk" onclick="dataTableRowCheck('.$row->id.',this)">';
            })
            ->editColumn('id', function ($query) {
                return '#'. $query->id;
            })
            ->editColumn('name', function ($query) {
                // Fix for name display
                $data = $query->users;
                return view('user::backend.users.user_details', compact('data'));
            })
            ->editColumn('role', function ($query) {
                // Fix for role display
                $user = $query->users;
                if ($user) {
                    return ucfirst($user->user_type ?? '-');
                }
                return '-';
            })
            ->editColumn('subject', function($query) {
                return $query->subject;
            })
            ->editColumn('datetime', function ($query) {
                $date = Setting::formatDate($query->updated_at);
                $time = Setting::formatTime($query->updated_at);
                return $date . ' ' . $time;
            })
            ->editColumn('mode', function ($query) {
                return ucfirst($query->mode) ?? '-';
            })
            ->editColumn('status', function ($query) {
                $status = $query->status;
                if($status == 0) {
                    return '<span class="badge text-success bg-success-subtle">' . __('messages.open') . '</span>';
                }
                return '<span class="badge text-danger bg-danger-subtle">' . __('messages.closed') . '</span>';
            })
            ->addColumn('action', function ($data) {
                return view('helpdesk::backend.helpdesk.action', compact('data'));
            })
            ->addIndexColumn()
            ->rawColumns(['check', 'subject', 'action', 'status'])
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

      public function create(Request $request)
    {
            
        $pageTitle1 = trans('messages.setting');
        $page = 'taxes';
        $id = $request->id;
        $auth_user = auth()->user();
        $assets = ['textarea'];
        $helpdesk = HelpDesk::find($id);
        $pageTitle = trans('messages.update_form_title',['form'=>'']);
        
        if($helpdesk == null){
            $pageTitle = trans('messages.add_button_form',['form' => '']);
            $helpdesk = new HelpDesk;
        }
        $users = User::whereNot('user_type', 'admin')->whereNot('user_type', 'demo_admin')->get();
        $module_title=__('messages.new_helpdesk');
      return view('helpdesk::backend.helpdesk.create', compact('assets','pageTitle' ,'helpdesk' ,'auth_user','users','pageTitle1','page','module_title' ));
    }

    public function store(HelpdeskRequest $request)
    {
        $helpdesk = $request->all();
        $helpdesk['contact_number']=$helpdesk['phone_number'] ?? null;
        $helpdesk['user_id'] = !empty($request->user_id) ? $request->user_id : auth()->id(); 
        $result = HelpDesk::updateOrCreate(['id' => $request->id], $helpdesk);  
        $this->logActivity('create',$result,'helpdesk_create');
        $activity_data = [
            'activity_type' => 'add_helpdesk',
            'notification_type'=>'add_helpdesk',
            'helpdesk_id' => $result->id,
            'sender_id' => $result->user_id,
            'receiver_id' => User::where('user_type', 'admin')->first()->id,
            'helpdesk' => $result,
        ];
    
     $this->sendNotification($activity_data);
     $this->addActivityData($activity_data);

        $this->storeAttachments($request, 'helpdesk_attachment', $result);
      
        $message = __('messages.update_form',[ 'form' => __('messages.helpdesk') ] );
		if($result->wasRecentlyCreated){
			$message = __('messages.save_form',[ 'form' => __('messages.helpdesk') ] );
		}
        if($request->is('api/*')) {
            $response = [
                'message'=>$message
            ];
            return comman_custom_response($response);
		}
        $msg=__('messages.record_add');
        return redirect()->route('backend.helpdesks.index')->with('success', $message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Helpdesk::findOrFail($id);
        $module_title=__('messages.edit_helpdesk');
    return view('helpdesk::backend.helpdesk.edit', compact('data','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(HelpdeskRequest $request, Helpdesk $helpdesk)
    {
        $requestData = $request->all();
        $helpdesk->update($requestData);
        $this->logActivity('update',$helpdesk,'helpdesk_update');
        $message=__('messages.update_form');
        return redirect()->route('backend.helpdesks.index')->with('success',$message);
    }

    public function show($id)
    {
        $auth_user = auth()->user();
        $helpdeskdata = HelpDesk::with('helpdeskactivity')->where('id',$id)->first();
        if(empty($helpdeskdata))
        {
            $msg = __('messages.not_found_entry',['name' => __('messages.helpdesk')] );
            return redirect(route('backend.helpdesks.index'))->withError($msg);
        }
        if ($helpdeskdata->user_id != auth()->id() && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
            return redirect(route('backend.helpdesks.index'))->withErrors(trans('messages.record_not_found'));
        }

        $formattedDate = Setting::formatDate($helpdeskdata->updated_at);
        $formattedTime = Setting::formatTime($helpdeskdata->updated_at);

        $datetime = $formattedDate . ' ' . $formattedTime;

        $pageTitle = trans('messages.query' ) .' '. trans('messages.detail' );
        $assets = ['datatable'];
        
        return view('helpdesk::backend.helpdesk.view', compact('pageTitle','auth_user','assets','helpdeskdata','datetime'));

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Helpdesk::findOrFail($id);
        $data->delete();
        $this->logActivity('delete',$data,'helpdesk_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Helpdesk::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'helpdesk_restore');
        $message = __('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Helpdesk::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'helpdesk_force_delete');
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }


    public function action(Request $request){
        $id = $request->id;
        $helpdesk = HelpDesk::withTrashed()->where('id',$id)->first();
        $msg = __('messages.not_found_entry',['name' => __('messages.helpdesk')] );
        if($request->type === 'restore'){
            $helpdesk->restore();
            $this->logActivity('restore',$helpdesk,'helpdesk_restore');
            $msg = __('messages.msg_restored',['name' => __('messages.helpdesk')] );
        }

        if($request->type === 'forcedelete'){
            $helpdesk->forceDelete();
            $this->logActivity('force_delete',$helpdesk,'helpdesk_force_delete');
            $msg = __('messages.msg_forcedelete',['name' => __('messages.helpdesk')] );
        }

        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }
        public function closed(Request $request, $id){
            
            $helpdesk = HelpDesk::where('id', $id)->first();

            if ($helpdesk && $helpdesk->status == 0) {
                $helpdesk->update(['status' => 1]);
                if(auth()->user()->hasRole(['admin', 'demo_Admin'])) {
                    $receiver = User::find($helpdesk->user_id);
                } else {
                    $receiver = User::where('user_type', 'admin')->first();
                }

                $receiver_id = $receiver->id ?? null;
                $receiver_type = $receiver->user_type ?? null;


                $activity_data = [
                    'activity_type' => 'closed_helpdesk',
                    'notification_type' => 'closed_helpdesk',
                    'helpdesk_id' => $helpdesk->id,
                    'sender_id' => auth()->id(),
                    'receiver_type' => $receiver_type,
                    'receiver_id' => $receiver_id,
                    'helpdesk' => $helpdesk
                ];
                $this->sendNotification($activity_data);
                $this->addActivityData($activity_data);
                $message = __('messages.closed_successfully', [ 'id' => $helpdesk->id ]);
                if(request()->is('api/*')){
                    return comman_custom_response(['message'=> $message , 'status' => true]);
                }
                return redirect()->route('backend.helpdesks.index')->withSuccess($message);
            }elseif ($helpdesk && $helpdesk->status == 1){
                $message = __('messages.already_closed_successfully', [ 'id' => $helpdesk->id ]);
                if(request()->is('api/*')){
                    return comman_custom_response(['message'=> $message , 'status' => true]);
                }
                return redirect()->route('backend.helpdesks.index')->withSuccess($message);
            }
            if(request()->is('api/*')){
                $message = __('messages.record_not_found');
                return comman_custom_response(['message'=> $message , 'status' => true]);
            }
            return redirect()->route('backend.helpdesks.index')->withError(__('messages.record_not_found'));

        }
    public function activity(Request $request,$id){

        $helpdesk = HelpDesk::where('id', $id)->first();

        if ($helpdesk && $helpdesk->status == 0) {
            $helpdeskactivity['helpdesk_id'] = $helpdesk->id;
            $helpdeskactivity['sender_id'] = auth()->id();
            if(auth()->user()->hasRole(['admin', 'demo_Admin'])) {
                $helpdeskactivity['receiver_id'] = $helpdesk->user_id;
            } else {
                $helpdeskactivity['receiver_id'] =\App\Models\User::where('user_type', 'admin')->first()->id ?? null;
            }
            $receiver = \App\Models\User::find($helpdeskactivity['receiver_id']);

            $helpdeskactivity['receiver_type'] = $receiver->user_type ?? null; 
            $helpdeskactivity['messages'] = $request->description ?? null;

            $activity = Helpdeskactivity::updateOrCreate(
                [
                    'helpdesk_id' => $helpdeskactivity['helpdesk_id'],
                    'sender_id' => $helpdeskactivity['sender_id'],
                    'receiver_id' => $helpdeskactivity['receiver_id'],
                    'messages' => $helpdeskactivity['messages'],
                ],
                $helpdeskactivity 
            );
            $activity_data = [
                'activity_type' => 'reply_helpdesk',
                'notification_type'=>'reply_helpdesk',
                'helpdesk_id' => $helpdeskactivity['helpdesk_id'],
                'sender_id' => $helpdeskactivity['sender_id'],
                'receiver_id' => $helpdeskactivity['receiver_id'],
                'messages' => $helpdeskactivity['messages'],
                'helpdesk' => $helpdesk,
                'receiver_type' => $helpdeskactivity['receiver_type'],
            ];
            $this->sendNotification($activity_data);
            $this->addActivityData($activity_data);
            $this->storeAttachments($request, 'helpdesk_activity_attachment', $activity);
            
            $message = __('messages.message_successfully_send' );
            if(request()->is('api/*')){
                return comman_custom_response(['message'=> $message , 'status' => true]);
            }
            return redirect()->route('backend.helpdesks.show', $helpdesk->id)->withSuccess($message);
        }
        if(request()->is('api/*')){
            $message = __('messages.record_not_found');
            return comman_custom_response(['message'=> $message , 'status' => true]);
        }
        return redirect()->route('backend.helpdesks.index')->withError(__('messages.record_not_found'));

    }
    private function storeAttachments($request, $attachmentPrefix, $data)
    {
   
        $file = [];

        if ($request->is('api/*')) {
            if ($request->has('attachment_count')) {
                for ($i = 0; $i < $request->attachment_count; $i++) {
                    $attachment = "{$attachmentPrefix}_{$i}";
                    if ($request->$attachment != null) {
                        $file[] = $request->$attachment;
                    }
                }
                storeMediaFile($data, $file, $attachmentPrefix);
            }
        } else {

            if ($request->hasFile($attachmentPrefix)) {
                
                storeMediaFile($data, $request->file($attachmentPrefix), $attachmentPrefix);
            }	
        }


        
    }

    public function addActivityData($data){

        $helpdesk = $data['helpdesk'];
        $id = $helpdesk->id;
        $employeeId = [$helpdesk->user_id];
        switch ($data['activity_type']) {
        case "add_helpdesk":
            $date = $helpdesk->updated_at ?? null;
            $data['activity_type'] = __('messages.add_helpdesk');
            $data['helpdesk_id'] = $helpdesk->id;
            $data['sender_id'] =  is_array($data['sender_id']) ? $data['sender_id'][0] : $data['sender_id'];;

            $data['receiver_id'] = is_array($data['receiver_id']) ? $data['receiver_id'][0] : $data['receiver_id'];
            $sender = \App\Models\User::find($data['sender_id']);
            $data['sender_name'] = $sender->display_name ?? 'New Sender';
        
            $receiver = \App\Models\User::find($data['receiver_id']);
            $data['receiver_name'] = $receiver->display_name ?? 'New Receiver';
            $data['activity_message'] = __('messages.created_by_helpdesk', [
                'name' => $data['sender_name'],
                'date' => $date
            ]);
            $data['messages'] = $helpdesk->description ?? '';
            $data['subject'] = $helpdesk->subject ?? '';
            $activity = Helpdeskactivity::updateOrCreate(
                [
                    'helpdesk_id' => $data['helpdesk_id'],
                    'sender_id' => $data['sender_id'],
                    'receiver_id' => $data['receiver_id'],
                    'messages' => $data['messages'],
                ],
                $data 
            );
            break;
        case "closed_helpdesk":
            $date = $helpdesk->updated_at ?? null;
            $data['activity_type'] = __('messages.closed_helpdesk');
            $data['helpdesk_id'] = $helpdesk->id;
            $providerId = [$data['receiver_id']];
            $handymanId = [$data['receiver_id']];
            $data['sender_id'] =  is_array($data['sender_id']) ? $data['sender_id'][0] : $data['sender_id'];;

            $data['receiver_id'] = is_array($data['receiver_id']) ? $data['receiver_id'][0] : $data['receiver_id'];
            $sender = \App\Models\User::find($data['sender_id']);
            $data['sender_name'] = $sender->display_name ?? 'New Sender';
            $userId = $data['receiver_id'];
            $receiver = \App\Models\User::find($data['receiver_id']);
            $data['receiver_type'] = $receiver->user_type ?? 'admin';
            $data['receiver_name'] = $receiver->display_name ?? 'New Receiver';
            $data['activity_message'] = __('messages.closed_by_helpdesk', [
                'name' => $data['sender_name'],
                'date' => $date
            ]);
            $data['messages'] = __('messages.closed_by_helpdesk', [
                'name' => $data['sender_name'],
                'date' => $date
            ]);
            $activity = Helpdeskactivity::updateOrCreate(
                [
                    'helpdesk_id' => $data['helpdesk_id'],
                    'sender_id' => $data['sender_id'],
                    'receiver_id' => $data['receiver_id'],
                    'messages' => $data['messages'],
                ],
                $data 
            );
            break;
        case "reply_helpdesk":
            $date = $helpdesk->updated_at ?? null;
            $providerId = [$data['receiver_id']];
            $handymanId = [$data['receiver_id']];
            
            $data['activity_type'] = __('messages.replied_helpdesk');
            $data['helpdesk_id'] = $helpdesk->id;
            $data['sender_id'] =  is_array($data['sender_id']) ? $data['sender_id'][0] : $data['sender_id'];;

            $data['receiver_id'] = is_array($data['receiver_id']) ? $data['receiver_id'][0] : $data['receiver_id'];
            $userId = $data['receiver_id'];
            $sender = \App\Models\User::find($data['sender_id']);
            $data['sender_name'] = $sender->display_name ?? 'New Sender';
        
            $receiver = \App\Models\User::find($data['receiver_id']);
            $data['receiver_type'] = $receiver->user_type ?? 'admin';
            $data['receiver_name'] = $receiver->display_name ?? 'New Receiver';
            $data['activity_message'] = __('messages.replied_by_helpdesk', [
                'name' => $data['sender_name'],
                'date' => $date
            ]);
            $data['messages'] = is_array($data['messages']) ? $data['messages'][0] : $data['messages'];

            $activity = Helpdeskactivity::updateOrCreate(
                [
                    'helpdesk_id' => $data['helpdesk_id'],
                    'sender_id' => $data['sender_id'],
                    'receiver_id' => $data['receiver_id'],
                    'messages' => $data['messages'],
                ],
                $data 
            );
            break;
            default:
            $activity_data = [];
            break;
    }
    }
}
