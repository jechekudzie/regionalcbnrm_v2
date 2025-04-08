<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class OrganisationRolesController extends Controller
{
    //
    public function index(Organisation $organisation)
    {

        $roles = $organisation->organisationRoles;
        return view('admin.organisation_roles.index', compact('roles', 'organisation'));
    }

    //add store method for spatie roles with organisation
    public function store(Request $request, Organisation $organisation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $organisation->organisationRoles()->create([
            'name' => strtolower($request->name),
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.organisation-roles.index', $organisation)
            ->with('success', 'Role created successfully.');
    }

    //update role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update([
            'name' => strtolower($request->name),
            'guard_name' => 'web',
        ]);

        $organisation = Organisation::find($role->organisation_id);

        return redirect()->route('admin.organisation-roles.index', $organisation->slug)
            ->with('success', 'Role updated successfully.');
    }

    //destroy role
    public function destroy(Role $role)
    {

        $organisation = Organisation::find($role->organisation_id);
        $role->delete();

        return redirect()->route('admin.organisation-roles.index', $organisation->slug)
            ->with('success', 'Role deleted successfully.');
    }
}
