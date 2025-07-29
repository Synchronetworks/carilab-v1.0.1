<?php

namespace Modules\User\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\User\Http\Requests\UserRequest;
use Modules\User\Http\Requests\PasswordRequest;
use App\Trait\ModuleTrait;
use Hash;
use Modules\Subscriptions\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiringSubscriptionEmail;
use Modules\Appointment\Models\Appointment;
use Modules\Review\Models\Review;
use \App\Models\Setting;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
class UsersController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\UserExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'Users', // module title
            'users', // module name
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
        $type = $request->type;
        $module_title = ($type == 'soon-to-expire') ? __('messages.plan_expire') : 'Users';
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
                'value' => 'user_type',
                'text' => __('messages.lbl_user_type'),
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

        return view('user::backend.users.index_datatable', compact('module_action', 'module_title', 'filter', 'export_import', 'export_columns', 'export_url', 'type'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'User'; // Adjust as necessary for dynamic use

        return $this->performBulkAction(User::class, $ids, $actionType, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        if ($request->type == 'customer') {
            $query = User::myUser()->where('user_type', 'user')->withTrashed();
        } else {
            $query = User::whereNot('user_type', 'admin')->withTrashed();
        }

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
            $query = User::where('user_type', 'vendor')->whereIn('id', $userIds);
        }
        $filter = $request->filter;

       
        if (isset($filter['email'])) {

            $query->where('email', $filter['email']);
        }

        if (!empty($filter['name'])) {
                $query->where('id', $filter['name']);
            
        }
           // User Type Filter
        if (!empty($filter['user_type'])) {
            $query->where('user_type', $filter['user_type']);
        }

        // Gender Filter
        if (!empty($filter['gender'])) {
            $query->where('gender', $filter['gender']);
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
            ->editColumn('action', function ($data) use ($filterValue) {
                if($filterValue){
                    $data['user_type'] = $filterValue;
                }
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

            ->filterColumn('start_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(start_date, '%D %M %Y') like ?", ["%$keyword%"]);
            })
            ->filterColumn('end_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(end_date, '%D %M %Y') like ?", ["%$keyword%"]);
            })
            ->filterColumn('amount', function ($query, $keyword) {
                // Remove any non-numeric characters except for the decimal point
                $cleanedKeyword = preg_replace('/[^0-9.]/', '', $keyword);

                // Check if the cleaned keyword is not empty
                if ($cleanedKeyword !== '') {
                    // Filter the query by removing non-numeric characters from the amount column
                    $query->whereRaw("CAST(REGEXP_REPLACE(amount, '[^0-9.]', '') AS DECIMAL(10, 2)) LIKE ?", ["%{$cleanedKeyword}%"]);
                }
            })
            ->filterColumn('total_amount', function ($query, $keyword) {

                $cleanedKeyword = preg_replace('/[^0-9.]/', '', $keyword);

                if ($cleanedKeyword !== '') {
                    $query->whereRaw("CAST(REGEXP_REPLACE(total_amount, '[^0-9.]', '') AS DECIMAL(10, 2)) LIKE ?", ["%{$cleanedKeyword}%"]);
                }
            })


            ->editColumn('gender', function ($data) {
                return $data->gender ? ucwords($data->gender) : '-';
            })

            ->editColumn('user_type', function ($data) {
                return $data->user_type === 'user' ? 'Customer' : ucfirst($data->user_type);
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
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

    public function create()
    {
        $countries = Country::all(); // Get all countries
        $states = State::all(); // Get all states
        $cities = City::all(); // Get all cities
        $module_action = trans('messages.new');
        $module_title = trans('messages.user');
        return view('user::backend.users.form', compact('countries','states','cities','module_title','module_action'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->except('profile_image');

        $data['password'] = Hash::make($data['password']);



        $user = User::create($data);
        $this->logActivity('create', $user, 'user_create');
        if ($request->has('profile_image')) {
            $request->file('profile_image');

            storeMediaFile($user, $request->file('profile_image'), 'profile_image');
        }
        $user->assignRole($data['user_type']);

        $message = trans('messages.create_form');
        return redirect()->route('backend.users.index')->with('success', $message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = User::find($id);
   

        $countries = Country::all(); // Get all countries
        $states = State::all(); // Get all states
        $cities = City::all(); // Get all cities
        $module_action = trans('messages.edit');
        $module_title = trans('messages.user');
        return view('user::backend.users.form', compact('data','countries','states','cities','module_action','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {


        $data = $request->except('profile_image');
    
        $user = User::where('id', $id)->first();

        $user->update($data);
        $this->logActivity('update', $user, 'user_update');
        if ($request->has('profile_image')) {
            $request->file('profile_image');

            storeMediaFile($user, $request->file('profile_image'), 'profile_image');
        }

        $message = trans('messages.update_form');

        return redirect()->route('backend.users.index')->with('success', $message);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = User::find($id);
        $data->delete();
        $this->logActivity('delete', $data, 'user_delete');
        $message = trans('messages.delete_form');
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function restore($id)
    {
        $data = User::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'plan_restore');
        $message = trans('messages.restore_form', ['form' => 'plan']);
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function forceDelete($id)
    {
        $data = User::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'plan_force_delete');
        $message = trans('messages.permanent_delete_form', ['form' => 'plan']);
        return response()->json(['message' => $message, 'status' => true], 200);
    }


    public function update_status(Request $request, User $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function changepassword($id)
    {

        $id = $id;
        return view('user::backend.users.changepassword', compact('id'));

    }

    public function updatePassword(PasswordRequest $request, $id)
    {

        $user = User::where('id', $id)->first();

        if ($user == "") {
            $message = __('messages.user_not_found');
            return redirect()->route('backend.users.changepassword', ['id' => $id])->with('error', $message);
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('messages.same_pass');
                return redirect()->route('backend.users.changepassword', ['id' => $user->id])->with('error', $message);
            }

            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
            $message = __('messages.pass_successfull');
            return redirect()->route('backend.users.index', $user->id)->with('success', $message);
        } else {
            $message = __('messages.check_old_password');
            return redirect()->route('backend.users.changepassword', ['id' => $user->id])->with('error', $message);
        }


    }

    // expire user send mail
    public function sendEmail(Request $request)
{
    // Check if a specific user ID is passed
    if ($request->has('user_id')) {
        $userId = $request->user_id;  // Get the specific user ID
        $user = User::find($userId);  // Find the user by ID

        if ($user) {
            // Send email to this user
            $this->sendSubscriptionExpiryEmail($user);
            return response()->json(['message' => __('messages.email_sent_successfully')], 200);
        } else {
            return response()->json(['message' => __('messages.user_not_found')], 404);
        }
    } else {
        if (!$request->has('user_ids') || empty($request->user_ids)) {
            return response()->json(['message' => __('messages.no_user_selected')], 400);
        }
    
        $userIds = $request->user_ids;
        $users = User::whereIn('id', $userIds)->get(); // Get those users

        foreach ($users as $user) {
            $this->sendSubscriptionExpiryEmail($user);
        }
        return response()->json(['message' => __('messages.email_sent_successfully')], 200);
    }
}

private function sendSubscriptionExpiryEmail($user)
{
    if (isSmtpConfigured()) {
        Mail::to($user->email)->send(new ExpiringSubscriptionEmail($user));  // Send email to the user
    } else {
        return response()->json(['message' => __('messages.issue_with_mail_service'), 'status' => true], 200);
    }
}




    public function details(Request $request, $id)
    {
        $data = User::myUser()->with(['subscriptiondata','reviews'])->withTrashed()->where('id',$id)->first();
        if ($data == null) {
            if(auth()->user()->hasRole('vendor')){
                return redirect()->route('backend.home')->with('error',  __('messages.record_not_found'));
            }
            return redirect()->route('backend.users.index')->with('error',  __('messages.record_not_found'));
        }
        $show_name = $data->first_name . ' ' . $data->last_name;
        $module_title = $show_name ?? __('messages.user_details');
        $route = 'backend.users.index';
        $filter = [
            'status' => $request->status,
        ];


        $totalAppointments = Appointment::MyAppointment()->where('customer_id', $id)->count();
        $cancelledAppointments = Appointment::MyAppointment()->where('customer_id', $id)
            ->whereIn('status', ['cancelled','rejected'])
            ->count();
        $completedAppointments = Appointment::MyAppointment()->where('customer_id', $id)
            ->where('status', 'completed')
            ->count();
        $upcomingAppointments = Appointment::MyAppointment()->where('customer_id', $id)
            ->whereIn('status', ['pending', 'accept'])
            ->count();

        // Get payment statistics
        $totalPaidAmount = Appointment::MyAppointment()->whereHas('transactions', function($q){
            $q->where('payment_status','paid');
        })->where('customer_id', $id)->sum('total_amount');

        // Get review statistics
        $reviews = Review::visibleToUser(auth()->user())->where('user_id', $id)->get();
        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating') ?? 0;

        // Get rating counts for each star level
        $ratingCounts = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        return view('user::backend.users.details', compact(
            'data',
            'totalAppointments',
            'cancelledAppointments',
            'completedAppointments',
            'upcomingAppointments',
            'totalPaidAmount',
            'totalReviews',
            'averageRating',
            'ratingCounts',
            'module_title'
        ));
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
