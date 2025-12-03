<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): View
    {
        $client = $request->session()->get('client');
        
        return view('dashboard.index', [
            'client' => $client,
        ]);
    }
}
