<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountCreatedMail;
use App\Models\Admin\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class OrganisationUsersController extends Controller
{
    //
    public function index(Organisation $organisation)
    {
        $users = $organisation->users;
        $roles = $organisation->organisationRoles;

        return view('admin.organisation_users.index', compact('users', 'organisation', 'roles'));
    }


    //add store method for spatie users with organisation
    public function store(Request $request, Organisation $organisation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required',
            'organisation_id' => 'required',
        ]);

        $password = 'password@1';

        //check if user exists by email
        $user = User::where('email', $request['email'])->first();
        $role = Role::find($request['role_id']);
        $organisation = Organisation::find($request['organisation_id']);


        if ($user != null) {
            // Attach role with model_type and organisation_id
            $user->roles()->attach($role->id, [
                'model_type' => get_class($user),
                'organisation_id' => $organisation->id
            ]);
        } else {
            //create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
            ]);

            // Attach role with model_type and organisation_id
            $user->roles()->attach($role->id, [
                'model_type' => get_class($user),
                'organisation_id' => $organisation->id
            ]);

        }
        $organisation->users()->attach($user->id, ['role_id' => $role->id]);

        $organisation = $organisation->refresh();
        //send email to user
        //Mail::to($user->email)->queue(new AccountCreatedMail($user->id, $organisation->id));

        return redirect()->route('admin.organisation-users.index', $organisation->slug)
            ->with('success', 'User created successfully.');
    }


    //update role
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required',
            'organisation_id' => 'required',
        ]);

        // Get current organisation
        $organisation = Organisation::find($request['organisation_id']);

        // Update user details
        $user->update($request->only('name', 'email'));

        // Get the new role
        $newRole = Role::find($request['role_id']);

        // Remove any existing roles associated with this user for the specific organisation
        // This assumes a user has only one role per organisation
        $user->roles()->wherePivot('organisation_id', $organisation->id)->detach();

        // Attach the new role to the user for the specified organization with model_type
        $user->roles()->attach($newRole, [
            'organisation_id' => $organisation->id,
            'model_type' => get_class($user)
        ]);

        // Update the role_id in the organisation_users pivot table
        $organisation->users()->updateExistingPivot($user->id, ['role_id' => $newRole->id]);


        return redirect()->route('admin.organisation-users.index', $organisation->slug)
            ->with('success', 'User updated successfully.');
    }

    //desrtroy role
    public function destroy($userId, $organisationId)
    {
        $user = User::findOrFail($userId);
        $organisation = Organisation::findOrFail($organisationId);

        // Check if the user is an admin in any organisation
        $isAdminElsewhere = $user->roles()->where('name', 'admin')->exists();

        if ($isAdminElsewhere) {
            // Detach the user from the specified organisation only
            $organisation->users()->detach($user->id);
            $user->roles()->wherePivot('organisation_id', $organisation->id)->detach();
        } else {
            // If the user is not an admin elsewhere, you might decide to delete them
            // or handle them differently. Adjust this part as per your application logic.
            // For example, just detach from the organisation:
            $organisation->users()->detach($user->id);
            $user->roles()->wherePivot('organisation_id', $organisation->id)->detach();
        }

        return redirect()->route('admin.organisation-users.index', $organisation->slug)
            ->with('success', 'User detached from the organisation successfully.');
    }

}
