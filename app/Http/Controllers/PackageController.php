<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;

class PackageController extends Controller
{
    /**
     * Display a listing of available packages.
     */
    public function index()
    {
        $packages = Package::where('is_active', true)->get();
        
        return view('packages.index', compact('packages'));
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package)
    {
        if (!$package->is_active) {
            abort(404);
        }
        
        return view('packages.show', compact('package'));
    }
}
