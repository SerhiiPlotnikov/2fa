<?php

namespace App\Http\Controllers;

use App\Services\AuthyAuthentication;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        return view('home');
    }
}
