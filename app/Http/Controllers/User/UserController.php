<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\SendRegistrationEmail;
use App\Models\IdType;
use App\Models\InternalUserRole;
use App\Models\InternalUsersHasInternalUserRole;
use App\Models\RolePermission;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Eddytim\Auditlog\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use function back;
use function response;
use function view;

class UserController extends Controller {

    public function __construct(){
        $this->middleware('2fa');
    }

    public function dashboard(){
        if (!Auth::user()->hasPermission('view_dashboard')){
            $code = 401;
            return view('custom_error_page', compact('code'));
        }

        $daily_summery = DB::table('customer_application_bills_daily')->select('amount_tzs', 'amount_usd', 'services_id', 'created_at')
            ->whereDay('created_at', '=', date('d'))->get();
        $weekly_summery = DB::table('customer_application_bills_weekly')->select('amount_tzs', 'amount_usd', 'services_id', 'created_at')
            ->where('created_at', '>=', Carbon::now()->toDateString().' 00:00:00')->get();
        $monthly_summery = DB::table('customer_application_bills_monthly')->select('amount_tzs', 'amount_usd', 'services_id', 'created_at')
            ->whereYear('created_at', '=', date('Y'))->get();
        $yearly_summery = DB::table('customer_application_bills_yearly')->select('amount_tzs', 'amount_usd', 'services_id', 'created_at')
            ->whereYear('created_at', '=', date('Y'))->get();

        //        weekly amount
        $weekly_usd = 0;
        $weekly_tzs = 0;
        foreach ($weekly_summery as $weekly){
            $weekly_usd += $weekly->amount_usd;
            $weekly_tzs += $weekly->amount_tzs;
        }

        //        daily amount
        $daily_usd = 0;
        $daily_tzs = 0;
        foreach ($daily_summery as $daily){
            if (Carbon::parse($daily->created_at)->format('d') == date('d')){
                $daily_usd += $daily->amount_usd;
                $daily_tzs += $daily->amount_tzs;
            }
        }

        //        yearly amount
        $yearly_usd = 0;
        $yearly_tzs = 0;
        foreach ($yearly_summery as $yearly){
            if (Carbon::parse($yearly->created_at)->format('y') == date('y')){
                $yearly_usd += $yearly->amount_usd;
                $yearly_tzs += $yearly->amount_tzs;
            }
        }

//        monthly amount
        $current_month_earnings_usd = 0;
        $current_month_earnings_tzs = 0;
        $monthly_usd = [];
        $monthly_tzs = [];
        foreach ($monthly_summery as $monthly){
            if (Carbon::parse($monthly->created_at)->format('m') == date('m')){
                $current_month_earnings_usd += $monthly->amount_usd;
                $current_month_earnings_tzs += $monthly->amount_tzs;
            }
            $monthly_usd[] = $monthly->amount_usd;
            $monthly_tzs[] = $monthly->amount_tzs;
        }

//        dd($weekly_summery);
        $services = Service::all();

        AuditLog::store([
            'event_status' => 'SUCCESS',
            'event_type' => 'ViewItem',
            'user_id' => Auth::id(),
            'description' => Auth::user()->username.' has viewed the dashboard',
            'table_name' => null,
            'row_id' => null
        ]);

        return view('components.dashboard', compact('daily_summery', 'weekly_summery', 'weekly_usd', 'weekly_tzs',
            'current_month_earnings_tzs', 'current_month_earnings_usd', 'yearly_summery', 'services', 'monthly_usd',
            'monthly_tzs', 'monthly_summery', 'daily_usd', 'daily_tzs', 'yearly_tzs', 'yearly_usd'));
    }

    public function index() {
//        if (!Auth::user()->hasPermission('view_users_list')){
//            return back()->withErrors(['You are not Authorized!']);
//        }
        $query = '';
        $users = User::query()->select('id', 'first_name', 'last_name', 'username', 'email', 'phone_number', 'gender', 'id_types_id', 'id_number', 'account_status_code')->get();

        AuditLog::store([
            'event_status' => 'SUCCESS',
            'event_type' => 'ViewList',
            'user_id' => Auth::id(),
            'description' => Auth::user()->username.' has viewed users list',
            'table_name' => null,
            'row_id' => null
        ]);
        return view('admin.users.index', compact('users', 'query'));
    }

    public function create() {
        if (!Auth::user()->hasPermission('creating_users')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $roles = InternalUserRole::where('id', '<>', '1')->get();
        $permissions = RolePermission::all();
        $id_types = IdType::all();
        return view('admin.users.create', compact('roles', 'id_types', 'permissions'));
    }


    public function show($id){
        if (!Auth::user()->hasPermission('view_user_profile')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $user = DB::select('call sp_get_user_profile (?)', array(Crypt::decrypt($id)));
        $user_roles = DB::select('call sp_get_user_roles (?)', array(Crypt::decrypt($id)));
        $user_permissions = DB::select('call sp_get_user_permissions (?)', array(Crypt::decrypt($id)));
        $roles = DB::select('call sp_get_roles');
        $permissions = DB::select('call sp_get_permissions');

        
        // dd($permissions);
        $permission_user = DB::select('call sp_get_user_with_permissions (?)', array(Crypt::decrypt($id)));
        $user = $user[0];

        $u_roles_filtered = [];
        foreach ($user_roles as $user_role) {
            $u_roles_filtered[] = ['id'=> $user_role->role_id, 'name'=> $user_role->role_name];
           
        }

        $u_permissions_filtered = [];
        foreach ($user_permissions as $user_permission) {
            $u_permissions_filtered[] = $user_permission->permission_id;
        }


        AuditLog::store([
            'event_status' => 'SUCCESS',
            'event_type' => 'ViewItem',
            'user_id' => Auth::id(),
            'description' => 'Has viewed '.$user->username.' profile',
            'table_name' => null,
            'row_id' => null
        ]);

      
        $approves = is_dual_control_pending($user->account_status_code, $user->id, config('constants.dual_controls.create_new_user'));
        
        return view('admin.users.show', compact('user', 'roles','permission_user','permissions', 'u_roles_filtered', 'u_permissions_filtered','approves'));
    }

    public function searchUsers(Request $request){
        $query = $request['body'];
        if (empty($query)){
            $users = User::select('id', 'first_name', 'last_name', 'username', 'email', 'phone_number', 'gender', 'id_types_id', 'id_number', 'account_status_id')
                ->where('id', '<>', Auth::id())->get();
        }else{
            $users = DB::select('call sp_search_user(?)', array($query));
        }
        return response()->json(['results' => $users]);
    }

    public function addRemovePasswordPolicy(Request $request){
        $action = $request['action'];
        $policy_name = $request['policy_name'];

        $passwordPolicy = DB::select('call sp_insert_remove_password_policy(?,?)', array($action, $policy_name));

        AuditLog::store([
            'event_status' => 'SUCCESS',
            'event_type' => 'Edit',
            'user_id' => Auth::id(),
            'description' => Auth::user()->username.' has edited password policy',
            'table_name' => null,
            'row_id' => null
        ]);
        return response()->json(['results' => $passwordPolicy[0]]);
    }

    public function update(Request $request){
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'gender' => 'required',
        ]);

        $user = Auth::user();
        if ($user->update($request->all())){
            AuditLog::store([
                'event_status' => 'SUCCESS',
                'event_type' => 'Edit',
                'user_id' => Auth::id(),
                'description' => Auth::user()->username.' has updated his profile',
                'table_name' => null,
                'row_id' => null
            ]);
            return back()->with(['message' => 'User information updated successfully']);
        }

        AuditLog::store([
            'event_status' => 'FAILED',
            'event_type' => 'Edit',
            'user_id' => Auth::id(),
            'description' => Auth::user()->username.' has failed to update profile',
            'table_name' => null,
            'row_id' => null
        ]);
        return back()->withErrors(['Could not update user']);
    }

  

    public function editUser(Request $request, $id){
        $user = User::query()->where('id', Crypt::decrypt($id))->first();
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'email' => 'unique:internal_users,email,'.$user->id,
            'phone_number' =>  'unique:internal_users,phone_number,'.$user->id,
            'gender' => 'required',
        ]);

        // $user = User::query()->where('id', Crypt::decrypt($id))->first();
        $userRole = InternalUsersHasInternalUserRole::query()->where('internal_users_id',$user->id)->first();
        $userRole->update([
            'internal_user_roles_id' => $request->role,
        ]);

        if (!$user){
            return back()->with(['message' => 'User does not exist']);
        }
        $oldFName = $user->first_name;
        $oldLName = $user->last_name;
        if ($user->update($request->all())){
            AuditLog::store([
                'event_status' => 'SUCCESS',
                'event_type' => 'Edit',
                'user_id' => Auth::id(),
                'description' => 'Has updated profile of: '.$oldFName.' '.$oldLName,
                'table_name' => null,
                'row_id' => null
            ]);
            return back()->with(['message' => 'User information updated successfully']);
        }

        AuditLog::store([
            'event_status' => 'FAILED',
            'event_type' => 'Edit',
            'user_id' => Auth::id(),
            'description' => 'Has failed to update user profile',
            'table_name' => null,
            'row_id' => null
        ]);
        return back()->withErrors(['Could not update user']);

    }

    public function editUserView($id){
        if (!Auth::user()->hasPermission('edit_user')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $user = User::find(Crypt::decrypt($id));
        $roles = InternalUserRole::all();

        if (!$user){
            return back()->withErrors(['User does not exist']);
        }
        $id_types = IdType::all();
        return view('admin.users.edit', compact('id', 'id_types', 'user','roles'));
    }

    public function toggleBlock(Request $request){
        if (!Auth::user()->hasPermission('block_user')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'row_id' => 'required',
            'reason' => 'required'
        ]);
        $id = $request['row_id'];
        $user = User::query()->where('id', $id)->first();
        if (!$user){
            return back()->withErrors(['User does not exist']);
        }
        $userRole = InternalUsersHasInternalUserRole::query()->where('internal_users_id', $id)->first();
        if (!$userRole){
            return back()->withErrors(['User does not have a role']);
        }

        $status = 'AC001';
        if ($user->account_status_code == 'AC001'){
            $status = 'BL001';
        }

        DB::beginTransaction();
        try {
            if ($userRole->update(['internal_users_account_status_code' => $status])){
                if ($user->update(['account_status_code' => $status])){
                    DB::commit();
                    AuditLog::store([
                        'event_status' => 'SUCCESS',
                        'event_type' => 'Block',
                        'user_id' => Auth::id(),
                        'description' => 'Has successfully blocked '.$user->username,
                        'table_name' => null,
                        'row_id' => null
                    ]);
                    return back()->with(['message' => 'Operation success']);
                }
                DB::rollBack();
            }
        }catch (\Throwable $ex){
            DB::rollBack();
        }

        AuditLog::store([
            'event_status' => 'FAILED',
            'event_type' => 'Block',
            'user_id' => Auth::id(),
            'description' => 'Has failed to block '.$user->username,
            'table_name' => null,
            'row_id' => null
        ]);
        return back()->withErrors(['Operation failed']);
    }

    public function sendEmail(Request $request){
        $request->validate([
            'email' => 'required',
            'message' => 'required'
        ]);
    }

    public function makeApprover(Request $request){
        $request->validate([
            'user_id' => 'required',
            'action' => 'required',
        ]);

        $user = User::query()->lockForUpdate()->find($request['user_id']);
        if (!$user){
            AuditLog::store([
                'event_status' => 'FAILED',
                'event_type' => 'Edit',
                'user_id' => Auth::id(),
                'description' => 'Attempted to make/remove user as approver',
                'table_name' => null,
                'row_id' => null
            ]);
            return back()->withErrors(['User does not exist']);
        }
        if (!$user->update(['is_approver' => $request['action']])){
            AuditLog::store([
                'event_status' => 'FAILED',
                'event_type' => 'Edit',
                'user_id' => Auth::id(),
                'description' => 'Attempted to make/remove '.$user->username.' as approver',
                'table_name' => null,
                'row_id' => null
            ]);
            return back()->withErrors(['Could not update user']);
        }
        $message = 'Has made '.$user->username.' as Approver';
        if ($request['action'] == 0){
            $message = 'Has removed '.$user->username.' as Approver';
        }
        AuditLog::store([
            'event_status' => 'SUCCESS',
            'event_type' => 'Edit',
            'user_id' => Auth::id(),
            'description' => $message,
            'table_name' => null,
            'row_id' => null
        ]);
        return back()->with(['message' => 'User approve permission changed']);
    }

    public function approveUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
           ]);

        $user = User::query()->where('id',$request->user_id)->first();
        $password = $this->randomPassword();

        if (!$user){
            AuditLog::store([
                'event_status' => 'FAILED',
                'event_type' => 'Edit',
                'user_id' => Auth::id(),
                'description' => 'Attempted to approve user',
                'table_name' => null,
                'row_id' => null
            ]);
            return back()->withErrors(['User does not exist']);
        }
        $user->update(['account_status_code' => 'AC001','password' => Hash::make($password),
          ]);
        
        $full_name = $user->first_name.' '.$user->last_name;
        SendRegistrationEmail::dispatch($full_name, $user->username, $user->email, $password);
        Log::info('====  send registration email to user. =====');
        $message = 'Has approved '.$user->username.'';
        AuditLog::store([
            'event_status' => 'SUCCESS',
            'event_type' => 'Edit',
            'user_id' => Auth::id(),
            'description' => $message,
            'table_name' => null,
            'row_id' => null
        ]);
        return back()->with(['message' => 'User has approved succesful']);
    }

    public function rejectUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $user = User::query()->where('id',$request->user_id)->first();
        if (!$user){
            AuditLog::store([
                'event_status' => 'FAILED',
                'event_type' => 'Edit',
                'user_id' => Auth::id(),
                'description' => 'Attempted to approve user',
                'table_name' => null,
                'row_id' => null
            ]);
            return back()->withErrors(['User does not exist']);
        }
        
        $user->update(['account_status_code' => 'RJ001']);
    
        $message = 'Has approved '.$user->username.'';
    
        AuditLog::store([
            'event_status' => 'SUCCESS',
            'event_type' => 'Edit',
            'user_id' => Auth::id(),
            'description' => $message,
            'table_name' => null,
            'row_id' => null
        ]);
        return back()->with(['message' => 'User has rejected']);
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

}
