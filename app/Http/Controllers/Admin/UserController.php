<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class UserController extends Controller
{
    protected $data = [];
    public function __construct(){
        $this->data['users'] = User::all();
        $this->data['roles'] = Role::all();
        $this->data['permissions'] = Permission::all();
    }

    public function index(){
        return view("admin.users.index", $this->data);
    }

    public function create(){
        return view("admin.users.create");
    }

    public function store(Request $request)
    {
       
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();
    
        return redirect()->route('admin.users.index')
                        ->with('success','User created successfully');
    }

    public function show( User $user){
        $this->data["user"] = $user;
        return view("admin.users.role", $this->data);
    }

    public function assignRole(Request $request, User $user)
    {
        if ($user->hasRole($request->role)) {
            return back()->with("message", "Roles exists");
        }

        $user->assignRole($request->role);
        return back()->with("message", "Role Assigned");
    }

    public function removeRole(User $user, Role $role){
        if($user->hasRole($role)){
            $user->removeRole($role);
            return back()->with("message", "Role removed");
        }

        return back()->with("message", "Role does not exist");
    }

    public function givePermission(Request $request, User $user){
        if ($user->hasPermissionTo($request->permission)){
            return back()->with('message','Permission Exis');
        }
        $user->givePermissionTo($request->permission);
        return back()->with('message','Permission Added');
    }

    public function revokePermission(User $user, Permission $permission){
        if ($user->hasPermissionTo($permission)){
            $user->revokePermissionTo($permission);
            return back()->with('message','Permission revoked');
        }
        return back()->with('message','Permission exits');
    }
}
