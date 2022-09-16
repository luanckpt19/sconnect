<?php

namespace App\Http\Controllers\Ticket24;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Ticket24Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }
    
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home(Request $request) {        
        return view('ticket24.index'); 
    }
    public function setting(Request $request) {        
        return view('ticket24.setting'); 
    }

}
