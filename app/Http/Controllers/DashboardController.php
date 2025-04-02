<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Placeholder methods for the dashboard
    public function index()
    {
        return view('dashboard.index');
    }

    public function index2()
    {
        return view('dashboard.index2');
    }

    public function huntingDashboard()
    {
        return view('dashboard.hunting');
    }

    public function huntingDashboardByDistrict()
    {
        return view('dashboard.hunting-district');
    }

    public function huntingDashboardBySpecies()
    {
        return view('dashboard.hunting-species');
    }

    public function conflictDashboard()
    {
        return view('dashboard.conflict');
    }

    public function conflictDashboardByDistrict()
    {
        return view('dashboard.conflict-district');
    }

    public function conflictDashboardBySpecies()
    {
        return view('dashboard.conflict-species');
    }

    public function controlDashboard()
    {
        return view('dashboard.control');
    }

    public function controlDashboardByDistrict()
    {
        return view('dashboard.control-district');
    }

    public function controlDashboardBySpecies()
    {
        return view('dashboard.control-species');
    }

    public function incomeRecordsDashboard()
    {
        return view('dashboard.income');
    }

    public function incomeRecordsDashboardByDistrict()
    {
        return view('dashboard.income-district');
    }

    public function incomeRecordsDashboardBySpecies()
    {
        return view('dashboard.income-species');
    }

    public function incomeRecordDashboardBarChart()
    {
        return view('dashboard.income-bar-chart');
    }
} 