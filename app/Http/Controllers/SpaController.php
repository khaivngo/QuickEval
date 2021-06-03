<?php

/**
 * This is a SPA. So this is our only view. 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpaController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
