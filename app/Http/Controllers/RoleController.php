<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:عرض صلاحية'], ['only' => ['index'] ]);
        $this->middleware(['permission:أضافة صلاحية'], ['only' => ['create', 'store'] ]);
        $this->middleware(['permission:تعديل صلاحية'], ['only' => ['edit', 'update'] ]);
        $this->middleware(['permission:حذف صلاحية'], ['only' => ['destroy'] ]);
    }

    public function index(Request $request)
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permission = Permission::get();
        return view('roles.create', compact('permission'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
        ]);

        $permission_ids = [];

        foreach ($request->input('permission') as $permission_id){
            $permission_ids[] = intval($permission_id);
        }

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($permission_ids);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required|array',
        ]);

        $permission_ids = [];

        foreach ($request->input('permission') as $permission_id){
            $permission_ids[] = intval($permission_id);
        }

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($permission_ids);

        # ده الكود القديم الي مكنش شغال الي مش بيقرأ اسم ال array بيجيب ارقام ال array بس

//        $role->syncPermissions($permission_ids);
//        $role = Role::find($id);
//        $role->name = $request->input('name');
//        $role->save();
//
//        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        DB::table("roles")->where('id', $id)->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
