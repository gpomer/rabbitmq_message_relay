<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Redirect;
use Config;

// used for local cache busting and remote testing

class WelcomeController extends Controller
{



    /**
=
     * @param [type] $apikey
     * @param [type] $action
     * @param [type] $identifier
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->session()->has('loggedin')) {
            return view('documentation');
        } else {
            return view('landing');
        }

        

        // if user is logged in 
    }


    public function login(Request $request)
    {
        $input = $request->all();

        $username = $input['username'];
        $password = $input['password'];
        if($username === 'bunking' && $password == 'yuikopl'){
            $request->session()->put('loggedin', true);
        }

        return Redirect::to('/', 301);

    }

    public function logout(Request $request)
    {

        $request->session()->forget('loggedin');

        return view('landing');
       /// return Redirect::to('/', 301);

    }



}
