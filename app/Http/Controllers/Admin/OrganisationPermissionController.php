<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin\Organisation;
use Illuminate\Http\Request;

class OrganisationPermissionController extends Controller
{
    //index
    public function index(Organisation $organisation)
    {

        $modules = \App\Models\Module::all();

       $user = auth()->user();

       $permissions = $user->getFirstCommonRoleWithOrganization($organisation)->permissions;

       //dd($permissions->pluck('name')->toArray());

        return view('organisation.permissions.index',compact('modules','organisation','permissions'));
    }
}
