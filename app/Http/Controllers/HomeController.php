<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user->type == 1 || $user->type == 2)
            return view('desktop/index');
        else if($user->type == 3)
            return view('desktop/index2');
        else if($user->type == 4)
            return view('desktop/index3');
        else if($user->type == 5 || $user->type == 6)
            return view('desktop/index4');
    }
}
