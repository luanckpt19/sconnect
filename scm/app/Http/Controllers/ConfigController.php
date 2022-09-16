<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;

class ConfigController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('config');
    }
}
