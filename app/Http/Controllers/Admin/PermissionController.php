<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Organisation;

use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $modules = Module::all()->sortBy('id');
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('modules', 'permissions'));
    }

    public function store(Request $request)
    {
        if ($request->permission_type === 'module') {
            $request->validate([
                'name' => 'required|unique:modules,name',
            ]);
            
            $module = Module::create([
                'name' => ucfirst($request->name)
            ]);

            // Create default permissions
            $defaultActions = ['view', 'create', 'read', 'update', 'delete'];
            foreach ($defaultActions as $action) {
                $permissionName = $action . '-' . $module->slug;
                Permission::create(['name' => $permissionName]);
            }

            return redirect()->route('admin.permissions.index')
                           ->with('success', 'Module and default permissions created successfully');
        } else {
            // Handle individual permission creation
            $request->validate([
                'module_id' => 'required|exists:modules,id',
                'name' => 'required|string|max:255',
            ]);

            $module = Module::findOrFail($request->module_id);
            
            // Format the permission name: action-module
            $permissionName = strtolower(Str::slug($request->name)) . '-' . $module->slug;

            // Check if permission already exists
            if (Permission::where('name', $permissionName)->exists()) {
                return redirect()->back()
                               ->withErrors(['name' => 'This permission already exists for the selected module.']);
            }

            // Create the new permission
            Permission::create([
                'name' => $permissionName
            ]);

            return redirect()->route('admin.permissions.index')
                           ->with('success', 'Custom permission created successfully');
        }
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        
        // Check if it's a default permission
        $module = Module::where('slug', explode('-', $permission->name)[1])->first();
        if ($module) {
            $defaultActions = ['view', 'create', 'read', 'update', 'delete'];
            if (in_array(explode('-', $permission->name)[0], $defaultActions)) {
                return redirect()->back()
                               ->withErrors(['error' => 'Cannot delete default permissions.']);
            }
        }

        $permission->delete();
        return redirect()->route('admin.permissions.index')
                       ->with('success', 'Permission deleted successfully');
    }

   
    //assignPermission to roles
    public function assignPermission(Organisation $organisation, Role $role)
    {

        $permissions = Permission::all();
        $modules = Module::all();

        $permissions = Permission::all(); // Get all permissions
        // Retrieve all permissions associated with the role
        $rolePermissions = $role->permissions;

        return view('admin.permissions.assign', compact('organisation', 'role', 'permissions', 'modules', 'rolePermissions'));

    }

    public function assignPermissionToRole(Request $request, Organisation $organisation, Role $role)
    {
        // Retrieve selected permissions names from the request
        $permissions = $request->input('permissions', []);

        // Find existing permissions
        $permissionsToAssign = Permission::whereIn('name', $permissions)->get();

        // Sync permissions to the role (this will attach the new permissions and detach any that are not in the array)
        $role->syncPermissions($permissionsToAssign);

        return redirect()->route('admin.permissions.assign', [$organisation->slug, $role->id])
            ->with('success', 'Permissions assigned to ' . $role->name . ' successfully');
    }
}
