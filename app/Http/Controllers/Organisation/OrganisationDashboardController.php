<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\Admin\OrganisationType;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class OrganisationDashboardController extends Controller
{
    //
    public function dashboard()
    {
        $user = auth()->user();
        $organisations = $user->organisations;

        return view('organisation.dashboard.dashboard', compact('organisations', 'user' ));
    }

    public function index(Organisation $organisation)
    {
        $user = auth()->user();

        $userRole = $user->getFirstCommonRoleWithOrganization($organisation);


        return view('organisation.dashboard.show', compact('organisation', 'user', 'userRole'));
    }


    public function checkDashboardAccess(Organisation $organisation)
    {
        $user = auth()->user();

        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        $ruralDistrictCouncils = Organisation::where('organisation_type_id', $organisationType->id)->get();

        if ($user->hasPermissionTo('view-generic')) {
            return view('organisation.dashboard.organisations', compact('organisation', 'user', 'ruralDistrictCouncils'));
        } else {
            return view('organisation.dashboard.index', compact('organisation', 'user'));
        }
    }

    //ruralDistrictCouncils
    public function ruralDistrictCouncils()
    {
        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        $ruralDistrictCouncils = Organisation::where('organisation_type_id', $organisationType->id)->get();
        return view('organisation.dashboard.rural-district-councils', compact('ruralDistrictCouncils'));
    }
}
