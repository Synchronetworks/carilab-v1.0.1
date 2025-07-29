<?php

namespace Modules\User\Http\Controllers\Backend;

use App\Trait\ActivityLogger;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use Modules\User\Http\Requests\UserRequest;
use Hash;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use App\Trait\ModuleTrait;
use App\Models\UserOtherMapping;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
class CustomerController extends Controller
{
    use ActivityLogger;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected string $exportClass = '\App\Exports\UserExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'users.title', // module title
            'users', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = __('messages.list');
        $type = 'customer';
        $module_title = 'customer';
        $this->traitInitializeModuleTrait(
            $module_title,
            'users',
            'fa-solid fa-clipboard-list'
        );
        $export_import = true;
        $export_columns = [
            [
                'value' => 'first_name',
                'text' => __('messages.lbl_first_name'),
            ],
            [
                'value' => 'last_name',
                'text' => __('messages.lbl_last_name'),
            ],
            [
                'value' => 'email',
                'text' => __('messages.lbl_email'),
            ],
            [
                'value' => 'mobile',
                'text' => __('messages.lbl_contact_number'),
            ],
            [
                'value' => 'gender',
                'text' => __('messages.lbl_gender'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.status'),
            ]
        ];
        $export_url = route('backend.users.export');

        return view('user::backend.customer.index', compact('module_action', 'module_title', 'filter', 'export_import', 'export_columns', 'export_url', 'type'));
    }


    public function bulk_action(Request $request){
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'User';

        return $this->performBulkAction(User::class, $ids, $actionType, $moduleName);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $id = $request->id;


        $customerdata = User::find($id);
        $pageTitle = __('messages.update_form_title', ['form' => __('messages.user')]);
        $roles = Role::where('status', 1)->orderBy('name', 'ASC');

        $countries = Country::all(); // Get all countries
        $states = State::all(); // Get all states
        $cities = City::all(); // Get all cities
        if ($customerdata == null) {
            $pageTitle = __('messages.add_button_form', ['form' => __('messages.user')]);
            $customerdata = new User;
        }

        return view('user::backend.customer.create', compact('pageTitle', 'customerdata','countries','states','cities' ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->except('profile_image');

        $data['password'] = Hash::make($data['password']);
        $data['user_type'] = 'user';
        $user = User::create($data);
        $this->logActivity('create',$user,'customer_create');
        if ($request->has('profile_image')) {
            $request->file('profile_image');

            storeMediaFile($user, $request->file('profile_image'), 'profile_image');
        }
        $user->assignRole('user');

        $message = trans('messages.create_form');
        $messages=__('messages.record_add');
        return redirect()->route('backend.customer.index')->with('success',$messages);

    }

    public function index_data(Datatables $datatable, Request $request)
    {

        $query = User::where('user_type', 'user')->withTrashed();
        $filterValue = $request->type;
        if ($filterValue == 'soon-to-expire') {
            $query = User::role('user');
            $currentDate = Carbon::now();
            $expiryThreshold = $currentDate->copy()->addDays(7);
            $subscriptions = Subscription::with('user')
                ->where('status', 'active')
                ->whereDate('end_date', '<=', $expiryThreshold)
                ->get();
            $userIds = $subscriptions->pluck('user_id');
            $query = User::where('user_type', 'user')->whereIn('id', $userIds);
        }
        $filter = $request->filter;

        if (isset($filter['name'])) {
            $fullName = $filter['name'];

            $query->where(function ($query) use ($fullName) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$fullName%"]);
            });
        }
        if (isset($filter['email'])) {

            $query->where('email', $filter['email']);
        }

        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }
        return $datatable->eloquent($query)

            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" data-type="users" onclick="dataTableRowCheck(' . $data->id . ', this)">';
            })
            ->editColumn('name', function ($data) {
                return view('user::backend.users.user_details', compact('data'));
            })
            ->editColumn('mobile', function ($data) {
                if ($data->mobile != null) {
                    return $data->mobile;
                }
                return '-';
            })
            ->editColumn('gender', function ($data) {
                if ($data->gender != null) {
                    return $data->gender;
                }
                return '-';
            })
            ->editColumn('action', function ($data) {
                return view('user::backend.users.action_column', compact('data'));
            })
            ->editColumn('expire_date', function ($data) use ($filterValue) {
                if ($filterValue == 'soon-to-expire') {
                    $end_date = Carbon::createFromFormat('Y-m-d H:i:s', optional($data->subscriptionPackage)->end_date);
                    return Setting::formatDate($end_date);
                }
                return '-';
            })


            ->filterColumn('name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
                }
            })

            ->filterColumn('end_date', function ($query, $keyword) {
                try {
                    // Attempt to parse the keyword using the display format
                    $date = Carbon::createFromFormat('jS F Y', $keyword); // Adjust format based on your display format
                    $formattedDate = $date->format('Y-m-d'); // Convert to 'Y-m-d' for the query
    
                    // Apply the formatted date to the query for filtering
                    $query->whereDate('end_date', '=', $formattedDate);
                } catch (\Exception $e) {
                    // Fallback if parsing fails, use a generic LIKE query
                    $query->whereRaw("DATE_FORMAT(end_date, '%D %M %Y') like ?", ["%$keyword%"]);
                }
            })


            ->orderColumn('name', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)


            ->editColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : ''; // Set the checkbox to checked if status is true
                $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the user is soft-deleted
    
                return '
                <div class="form-check form-switch">
                    <input type="checkbox" data-url="' . route('backend.users.update_status', $row->id) . '" 
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
            })


            ->editColumn('mobile', function ($data) {
                return $data->mobile ? ucwords($data->mobile) : '-';
            })



            ->editColumn('updated_at', fn($data) => $this->formatUpdatedAt($data->updated_at))
            ->rawColumns(['action', 'name', 'status', 'check', 'gender'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);


    }
    private function formatUpdatedAt($updatedAt)
    {
        $diff = Carbon::now()->diffInHours($updatedAt);
        return $diff < 25 ? $updatedAt->diffForHumans() : $updatedAt->isoFormat('llll');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $auth_user = authSession();
        $customerdata = User::find($id);
        if (empty($customerdata)) {
            $msg = __('messages.not_found_entry', ['name' => __('messages.user')]);
            return redirect(route('user.index'))->withError($msg);
        }
        $customer_pending_trans = Payment::where('customer_id', $id)->where('payment_status', 'pending')->get();
        $pageTitle = __('messages.view_form_title', ['form' => __('messages.user')]);
        return view('customer.view', compact('pageTitle', 'customerdata', 'auth_user', 'customer_pending_trans'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data = User::find($id);

        $countries = Country::all(); // Get all countries
        $states = State::all(); // Get all states
        $cities = City::all(); // Get all cities
        $module_title = __('messages.lbl_edit_user');
        return view('user::backend.customer.create', compact('data', 'module_title','countries','states','cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
     
      
        $data = $request->except('profile_image');
        $user=User::where('id',$id)->first();

 $user->update($data);
 $this->logActivity('update',$user,'customer_update');
        if ($request->has('profile_image')) {
            $request->file('profile_image');
            if ($user->getMedia('profile_image')->first()) {
                $user->clearMediaCollection('profile_image');
            }
            storeMediaFile($user, $request->file('profile_image'), 'profile_image');
        }
       

        
        $message = trans('messages.update_form');

        return redirect()->route('backend.customer.index')->with('success', $message);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (demoUserPermission()) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $user = User::find($id);
        $msg = __('messages.msg_fail_to_delete', ['item' => __('messages.user')]);

        if ($user != '') {
            $user->delete();
            $this->logActivity('delete',$user,'customer_delete');
            $msg = __('messages.msg_deleted', ['name' => __('messages.user')]);
        }
        if (request()->is('api/*')) {
            return comman_message_response($msg);
        }
        return comman_custom_response(['message' => $msg, 'status' => true]);
    }
    public function action(Request $request)
    {
        if (demoUserPermission()) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;
        $user = User::withTrashed()->where('id', $id)->first();
        $msg = __('messages.not_found_entry', ['name' => __('messages.user')]);
        if ($request->type == 'restore') {
            $user->restore();
            $this->logActivity('restore',$user,'customer_restore');
            $msg = __('messages.msg_restored', ['name' => __('messages.user')]);
        }
        if ($request->type === 'forcedelete') {
            $user->forceDelete();
            $this->logActivity('force_delete',$user,'customer_force_delete');
            $msg = __('messages.msg_forcedelete', ['name' => __('messages.user')]);
        }
        if (request()->is('api/*')) {
            return comman_message_response($msg);
        }
        return comman_custom_response(['message' => $msg, 'status' => true]);
    }


    public function getChangePassword(Request $request)
    {
        $id = $request->id;
        $auth_user = authSession();

        $customerdata = User::find($id);
        $pageTitle = __('messages.change_password', ['form' => __('messages.change_password')]);
        return view('customer.changepassword', compact('pageTitle', 'customerdata', 'auth_user'));
    }

    public function changePassword(Request $request)
    {
        if (demoUserPermission()) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $user = User::where('id', $request->id)->first();

        if ($user == "") {
            $message = __('messages.user_not_found');
            return comman_message_response($message, 400);
        }

        $validator = \Validator::make($request->all(), [
            'old' => 'required|min:8|max:255',
            'password' => 'required|min:8|confirmed|max:255',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('password')) {
                $message = __('messages.confirmed', ['name' => __('messages.password')]);
                return redirect()->route('user.changepassword', ['id' => $user->id])->with('error', $message);
            }
            return redirect()->route('user.changepassword', ['id' => $user->id])->with('errors', $validator->errors());
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old, $hashedPassword);

        $same_exits = Hash::check($request->password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('messages.old_new_pass_same');
                return redirect()->route('user.changepassword', ['id' => $user->id])->with('error', $message);
            }

            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
            $message = __('messages.password_change');
            return redirect()->route('user.index')->withSuccess($message);
        } else {
            $message = __('messages.valid_password');
            return redirect()->route('user.changepassword', ['id' => $user->id])->with('error', $message);
        }
    }

    public function userLogin(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = \Auth::user();

        if ($request->login == 'user_login' && $user->user_type === 'user') {
            return redirect(RouteServiceProvider::FRONTEND);
        } elseif ($request->login == 'user_login' && $user->user_type !== 'user') {
            Auth::logout();
            return redirect()->back()->withErrors(['message' => __('messages.not_allow_log_in')]);
        } else {
            return redirect(RouteServiceProvider::HOME);
        }
    }

    public function other_members(Request $request)
    {
        $customer_id = $request->customer_id;
        $other_members = UserOtherMapping::where('user_id',$customer_id)->get();
        return response()->json($other_members);
    }

    public function checkUnique(Request $request)
{
    $field = $request->field;
    $value = $request->value;
    $id = $request->id;

    $query = User::where($field, $value);
    
    if ($id) {
        $query->where('id', '!=', $id);
    }

    $exists = $query->exists();

    return response()->json([
        'unique' => !$exists
    ]);
}
}