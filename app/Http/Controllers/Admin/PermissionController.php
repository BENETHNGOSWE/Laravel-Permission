<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    protected $data = [];
    public function __construct()
    {
        $this->data['permissions'] = Permission::all();
    }


    public function index()
    {
        return view("admin.permissions.index", $this->data);
    }

    public function create()
    {
        return view("admin.permissions.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required"
        ]);
        Permission::create($validated);

        return to_route("admin.permissions.index")->with("message", "Permission Created Successfully");
    }

    public function edit(Permission $permission)
    {
        $this->data["permission"] = $permission;
        $this->data['roles'] = Role::all();
        return view("admin.permissions.edit", $this->data);
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            "name" => "required"
        ]);
        $permission->update($validated);
        return to_route("admin.permissions.index")->with("message", "Permission Updated Successfully");
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with("message", "Permission Delete Successfully");
    }

    public function assignRole(Request $request, Permission $permission)
    {
        if ($permission->hasRole($request->role)) {
            return back()->with("message", "Roles exists");
        }

        $permission->assignRole($request->role);
        return back()->with("message", "Role Assigned");
    }

    public function removeRole(Permission $permission, Role $role){
        if($permission->hasRole($role)){
            $permission->removeRole($role);
            return back()->with("message", "Role removed");
        }

        return back()->with("message", "Role does not exist");
    }
}
