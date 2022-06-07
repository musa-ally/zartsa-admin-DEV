<?php

namespace App\Http\Controllers\RolesPermissions;

use App\Http\Controllers\Controller;
use App\Models\InternalRoleHasPermission;
use App\Models\InternalUserRole;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesPermissionController extends Controller
{
    public function index() {
        if (Auth::user()->hasPermission('view_roles_list') || Auth::user()->hasPermission('view_permission_list')){
            $roles = InternalUserRole::all();
            $permissions = RolePermission::all();
            return view('admin.roles_permissions.index', compact('roles', 'permissions'));
        }
        return back()->withErrors(['You are not Authorized!']);
    }

    public function storePermission(Request $request){
//        if (!Auth::user()->hasPermission('create_permission')){
//            return back()->withErrors(['You are not Authorized!']);
//        }
        $request->validate([
            'name' => 'required|unique:role_permissions',
            'description' => 'required'
        ]);

        $permission = new RolePermission();
        $permission->name = str_replace(' ', '_', strtolower($request['name']));
        $permission->display_name = $request['name'];
        $permission->description = $request['description'];
        if ($permission->save()){
            return redirect()->back()->with(['message' => 'Permission has been added successfully!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function storeRole(Request $request){
        if (!Auth::user()->hasPermission('create_role')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'name' => 'required|unique:internal_user_roles',
            'description' => 'required'
        ]);

        $role = new InternalUserRole();
        $role->name = str_replace(' ', '_', strtolower($request['name']));
        $role->display_name = $request['name'];
        $role->description = $request['description'];
        if ($role->save()){
            return redirect()->back()->with(['message' => 'Role has been added successfully!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function editRole(Request $request){
        if (!Auth::user()->hasPermission('edit_role')){
            return back()->withErrors(['You are not Authorized!']);
        }

        $request->validate([
            'role_id' => 'required',
            'name' => 'required',
            'description' => 'required'
        ]);

        $role = InternalUserRole::query()->where(['id' => $request->role_id])->first();
        if (!$role){
            return back()->withErrors(['Role does not exists']);
        }
        if($role->update([
            'name' => str_replace(' ', '_', strtolower($request->name)),
            'display_name' => $request->name,
            'description' => $request->description
        ])){
            return back()->with(['message' => 'Role has been updated!', 'error' => false]);
        }
        return back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function editPermission(Request $request){
        if (!Auth::user()->hasPermission('edit_permission')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'permission_id' => 'required',
            'name' => 'required',
            'description' => 'required'
        ]);

        $permission = RolePermission::query()->where('id', $request['permission_id'])->first();
        if (!$permission){
            return back()->withErrors(['Permission does not exists']);
        }
        if($permission->update([
            'name' => str_replace(' ', '_', strtolower($request['name'])),
            'display_name' => $request['name'],
            'description' => $request['description']
        ])){
            return back()->with(['message' => 'Permission has been updated!', 'error' => false]);
        }
        return back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function blockRole(Request $request){
        if (!Auth::user()->hasPermission('block_role')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'row_id' => 'required',
            'reason' => 'required'
        ]);
        $role = InternalUserRole::query()->where('id', $request['row_id'])->first();
        if (!$role){
            return back()->withErrors(['Role does not exist']);
        }
        $status = 'IA001';
        if ($role->service_status_code == 'IA001'){
            $status = 'AC001';
        }
        if ($role->update(['service_status_code' => $status])){
            return back()->with(['message' => 'Operation success']);
        }
        return back()->withErrors(['Operation failed']);
    }

    public function blockPermission(Request $request){
        if (!Auth::user()->hasPermission('block_permission')){
            return back()->withErrors(['You are not Authorized!']);
        }

        $request->validate([
            'row_id' => 'required',
            'reason' => 'required'
        ]);
        $permission = RolePermission::query()->where('id', $request['row_id'])->first();
        if (!$permission){
            return back()->withErrors(['Permission does not exist']);
        }
        $status = 'IA001';
        if ($permission->service_status_code == 'IA001'){
            $status = 'AC001';
        }
        if ($permission->update(['service_status_code' => $status])){
            return back()->with(['message' => 'Operation success']);
        }
        return back()->withErrors(['Operation failed']);
    }

    public function searchRoles(Request $request){
        $query = $request['body'];
        if (empty($query)){
            $roles = InternalUserRole::select('id', 'name', 'display_name', 'description', 'service_status_id')->get();
        }else{
            $roles = DB::select('call sp_search_roles(?)', array($query));
        }
        return response()->json(['results' => $roles]);
    }

    public function searchPermissions(Request $request){
        $query = $request['body'];
        if (empty($query)){
            $permissions = RolePermission::select('id', 'name', 'display_name', 'description', 'service_status_id')->get();
        }else{
            $permissions = DB::select('call sp_search_permissions(?)', array($query));
        }
        return response()->json(['results' => $permissions]);
    }

    public function showRole($name, $id){
        if (!Auth::user()->hasPermission('view_role_profile')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $roleId = Crypt::decrypt($id);
        $roles = InternalRoleHasPermission::query()->select('role_permissions_id')
            ->where('internal_user_roles_id', $roleId)->pluck('role_permissions_id');
        $permissions = RolePermission::all();
        return view('admin.roles_permissions.roles.show', compact('roles', 'permissions', 'name', 'roleId'));
    }

    public function addRemovePermissionToRole(Request $request){
        $action = $request['action'];
        $role_id = $request['role_id'];
        $permission_id = $request['permission_id'];

        $rolePermission = DB::select('call sp_insert_remove_role_permission(?,?,?)', array($action, $role_id, $permission_id));
        return response()->json(['results' => $rolePermission[0]]);
    }
}
