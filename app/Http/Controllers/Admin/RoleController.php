<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $data = [];
    public function __construct(){
        $this->data['roles']= Role::whereNotIn('name', ['admin'])->get();
    }

    public function index(){
        return view("admin.roles.index", $this->data);
    }

    public function create(){
        return view("admin.roles.create");
    }

    public function store(Request $request){
        $validated = $request->validate([
            "name"=> ["required","string","min:3"]
        ]);
        
        Role::create($validated);

        return to_route("admin.roles.index")->with('message', 'Role Created Successfully.');
    }

    public function edit(Role $role){
        $this->data["role"]=$role;
        $this->data['permissions'] = Permission::all();
        return view("admin.roles.edit", $this->data);
    }

    public function update(Request $request, Role $role){
        $validated = $request->validate([
            "name"=> ["required","string","min:3"]
        ]);
        $role->update($validated);
        return to_route("admin.roles.index")->with('message', 'Role Updated Successfully.');
    }

    public function destroy(Role $role) {
        $role->delete();
        return back()->with('message', 'Roles Deleted Successfully');
    }


    public function givePermission(Request $request, Role $role){
        if ($role->hasPermissionTo($request->permission)){
            return back()->with('message','Permission Exis');
        }
        $role->givePermissionTo($request->permission);
        return back()->with('message','Permission Added');
    }

    public function revokePermission(Role $role, Permission $permission){
        if ($role->hasPermissionTo($permission)){
            $role->revokePermissionTo($permission);
            return back()->with('message','Permission revoked');
        }
        return back()->with('message','Permission exits');
    }
}
