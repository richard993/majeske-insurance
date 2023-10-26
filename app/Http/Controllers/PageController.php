<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function index($page)
    {
        return view('pages.page'.$page);
    }

    public function pageProcess(Request $request)
    {
        dd($request->all());
    }
}
