<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categories;

class CategoriesController extends Controller
{
    public function index () {
        return Categories::where('user_id', auth()->user()->id)->get();
    }
}
