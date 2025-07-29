<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\UserCreated;
use App\Events\Backend\UserUpdated;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserAccountCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PasswordRequest;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;

class UserController extends Controller
{

    public function __construct()
    {

        $this->module_title = 'messages.myprofile';
        $this->module_name = 'users';
        $this->module_path = 'users';
        $this->module_icon = 'fa-solid fa-users';
        $this->module_model = "App\Models\User";

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }
    public function index()
    {
        return view('backend.users.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = auth()->user();

        $user->status = 0;

        $user->save();

        event(new UserUpdated($$module_name_singular));

        return response()->json(['message' => __('messages.account_deactivated')]);
    }



    public function emailConfirmationResend($id)
    {
       
        $user = User::where('id', '=', $id)->first();

        if ($user) {
            if ($user->email_verified_at == null) {

                $user->sendEmailVerificationNotification();

                flash('<i class="fas fa-check"></i>'.__('messages.email_sent'))->success()->important();

                return redirect()->back();
            } else {
                flash($user->name . __('messages.already_confirm_at') . $user->email_verified_at->isoFormat('LL'))->success()->important();

                return redirect()->back();
            }
        }
    }

    public function user_list(Request $request)
    {
        $term = trim($request->q);

        $role = $request->role;

        $query_data = [];

        $query_data = User::role(['user'])->where(function ($q) {
            if (! empty($term)) {
                $q->orWhere('first_name', 'LIKE', "%$term%")->$q->orWhere('last_name', 'LIKE', "%$term%");
            }
        })->active()->get();


        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'full_name' => $row->first_name . ' ' . $row->last_name,
                'email' => $row->email,
                'mobile' => $row->mobile,
                'gender' => $row->gender,
                'date_of_birth' => $row->date_of_birth,
                'profile_image' => $row->profile_image,
                'created_at' => $row->created_at,
            ];
        }

        return response()->json($data);
    }

    public function create_customer(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:3|max:191',
            'last_name' => 'required|min:3|max:191',
            'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|max:191|unique:users',
        ]);

        $data_array = $request->except('_token', 'roles', 'permissions', 'password_confirmation');
        $data_array['name'] = $request->first_name . ' ' . $request->last_name;

        if ($request->confirmed == 1) {
            $data_array = Arr::add($data_array, 'email_verified_at', Carbon::now());
        } else {
            $data_array = Arr::add($data_array, 'email_verified_at', null);
        }

        $user = User::create($data_array);

        $roles = $request['roles'];
        $permissions = $request['permissions'];

        // Sync Roles
        $roles = ['user'];
        $user->syncRoles($roles);

        \Artisan::call('cache:clear');

        event(new UserCreated($user));

        $message = __('user.user_created');

        if ($request->email_credentials == 1) {
            $data = [
                'password' => $request->password,
            ];

            try {
                $user->notify(new UserAccountCreated($data));
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }

            $message = __('user.account_crdential');
        }

        return response()->json(['data' => $user, 'message' => $message, 'status' => true]);
    }

    public function update_player_id(Request $request)
    {
        auth()->user()->update_player_id($request->player_id);

        return response()->json(['data' => $request->player_id, 'message' => __('messages.update_web_player_id'), 'status' => true]);
    }

    public function myProfile()
    {
        $user = Auth::user();
        $countries = Country::all(); // Get all countries
        $states = State::all(); // Get all states
        $cities = City::all(); // Get all cities
        $mediaUrls = $user->getProfileImageAttribute();
        return view('setting::backend.profile.section-pages.information-page', compact('user', 'mediaUrls','countries','states','cities'));
    }

    public function authData()
    {
        return response()->json(['data' => auth()->user(), 'status' => true]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = User::findOrFail($user->id);
        $request_data = $request->except('profile_image');
        $data->update($request_data);

        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        storeMediaFile($data, $request->file('profile_image'), 'profile_image');

        $message = __('messages.update_form', ['form' => __('customer.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function change_password(PasswordRequest $request)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }
        $user = Auth::user(); // Get the currently authenticated user

        $user_id = $user->id; // Retrieve the user's ID

        $data = User::findOrFail($user_id);

        $request_data = $request->only('old_password', 'password', 'password_confirmation');

        if (! Hash::check($request->old_password, $data->password)) {
            return response()->json(['message' => __('messages.old_password_mismatch'), 'status' => false], 403);
        }

        if ($request_data['password'] === $request_data['old_password']) {
            return response()->json(['message' => __('messages.new_password_mismatch'), 'status' => false], 403);
        }

        if ($request_data['password'] !== $request_data['password_confirmation']) {
            return response()->json(['message' => __('messages.password_mismatch'), 'status' => false], 403);
        }

        $request_data['password'] = Hash::make($request_data['password']);

        $data->update($request_data);

        $message = __('messages.password_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
