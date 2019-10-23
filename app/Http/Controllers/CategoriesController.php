<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoriesController extends Controller
{
    public function index () {
        return Category::where('user_id', auth()->user()->id)->get();
    }
}
