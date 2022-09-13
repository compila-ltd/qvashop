<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Role;
use App\Models\RoleTranslation;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_staff_roles'])->only('index');
        $this->middleware(['permission:add_staff_role'])->only('create');
        $this->middleware(['permission:edit_staff_role'])->only('edit');
        $this->middleware(['permission:delete_staff_role'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('id','!=',1)->paginate(10);
        return view('backend.staff.staff_roles.index', compact('roles'));

        // $roles = Role::paginate(10);
        // return view('backend.staff.staff_roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.staff.staff_roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->permissions);
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        $role_translation = RoleTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'role_id' => $role->id]);
        $role_translation->name = $request->name;
        $role_translation->save();

        flash(translate('New Role has been added successfully'))->success();
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $role = Role::findOrFail($id);
        return view('backend.staff.staff_roles.edit', compact('role','lang'));
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
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->syncPermissions($request->permissions);
        $role->save();

        // Role Translation
        $role_translation = RoleTranslation::firstOrNew(['lang' => $request->lang, 'role_id' => $role->id]);
        $role_translation->name = $request->name;
        $role_translation->save();

        flash(translate('Role has been updated successfully'))->success();
        return back();
        // return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        foreach ($role->role_translations as $key => $role_translation) {
            $role_translation->delete();
        }

        Role::destroy($id);
        flash(translate('Role has been deleted successfully'))->success();
        return redirect()->route('roles.index');
    }

    public function add_permission(Request $request)
    {
        $permission = Permission::create(['name' => $request->name, 'section'=> $request->parent]);
        return redirect()->route('roles.index');
    }

    public function create_admin_permissions(){
        
    }
}
