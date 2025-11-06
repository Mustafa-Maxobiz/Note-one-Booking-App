<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommandsController extends Controller
{
    /**
     * Display the commands page.
     */
    public function index()
    {
        return view('admin.commands.index');
    }
}
