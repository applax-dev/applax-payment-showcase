<?php

namespace App\Http\Controllers\SDK;

use App\Http\Controllers\Controller;

class ShowcaseController extends Controller
{
    public function index()
    {
        return view('sdk.showcase.index');
    }
}