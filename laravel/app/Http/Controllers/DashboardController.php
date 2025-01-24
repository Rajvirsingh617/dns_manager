<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $zoneCount = 0; // Default count for non-admins
        $totalZones = 0; // Default total count

            if (auth()->user()->role === 'admin') {
                // Admin sees all zones
                $totalZones = Zone::count();
                }else {
                // Regular user sees only their zones
                    $zoneCount = Zone::where('owner', Auth::id())->count();
                 }
        return view('layouts.dashboard', compact('zoneCount', 'totalZones'));
    }

}


