<?php

namespace App\Http\Controllers\UserManuals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class userManualsController extends Controller
{
    public function index(){
        $is_provider = \Auth::user()->is_provider();

        return view('manuales.manuales')->with('is_provider', $is_provider);
    }
}
