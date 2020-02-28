<?php

namespace App\Http\Controllers;

use App\Services\AuthyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class HomeController extends Controller
{
    private AuthyService $authy;

    public function __construct(AuthyService $authy)
    {
        $this->middleware('auth');
        $this->authy = $authy;
    }


    public function index(Request $request)
    {
//        $sid = 'AC01135b9ed148518d3e5e772d71119859';
//        $token = 'e6cc6c0030b7f42c10e5bb69618421c5';
//        $client = new Client($sid, $token);
//        dd($client);
        $user = $this->authy->registerUser($request->user());
        return view('home');
    }
}
