<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Constant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

	use AuthenticatesUsers {
		logout as performLogout;
	}

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request) {
		if ($request->isMethod('post')) {
			// POST login
			$messages = array(
				'required' => 'Chưa nhập email/mật khẩu',
				'email.email' => 'Email không hợp lệ',
				'password.min' => 'Mật khẩu chứa ít nhất 8 ký tự'
			);
			$rules = array(
				'email' => 'required|email', // make sure the email is an actual email
				'password' => 'required|min:8'
			);
			$validator = Validator::make($request->all(), $rules, $messages);
			if ($validator->fails()) {
				return redirect()->route('login')->withErrors($validator)->withInput($request->except('password'));
			}

			$userdata = array(
				'email' => $request->input('email') ,
				'password' => $request->input('password')
			);

			if (Auth::attempt($userdata)) {
				return redirect('/home');
			} else {
				// validation not successful, send back to form
				return redirect()->route('login')
					->withErrors(['email' => 'Đăng nhập không thành công'])->withInput($request->except('password'));
			}

		} else {
			// GET page login
			return view('auth.login');
		}

    }

    public function logout(Request $request) {
    	$this->performLogout($request);
    	return redirect()->route('login');
    }

    public function redirectToGoogle() {
    	return Socialite::driver('google')->redirect();
    }

    public function redirectToGooglePrompt() {
    	return Socialite::driver('google')
    	->with(["prompt" => "select_account"])->redirect();
    	// ->redirect();
    }

    public function handleGoogleCallback() {
    	try {
    		$googleUser = Socialite::driver('google')->user();
    		if (!Str::endsWith($googleUser->getEmail(), '@s-connect.net')) {
    			// Chỉ chấp nhận đối với email thuộc domain s-connect.net
    			return redirect('/login')->with(['domaiNotAuth' => 'Địa chỉ email bạn vừa dùng không thuộc SCONNECT']);
    		}

    		$finduser = User::where('google_id', $googleUser->id)->first();

    		if($finduser) {
    			$finduser->user_token = $googleUser->token;
    			$finduser->save();
    		} else {
    			$finduser = User::create([
    					'name' => $googleUser->user['name'],
    					'email' => $googleUser->user['email'],
    					'google_id'=> $googleUser->id,
    					'user_token'=> $googleUser->token,
    					'given_name' => $googleUser->user['given_name'],
    					'family_name' => $googleUser->user['family_name'],
    					'picture' => $googleUser->user['picture'],
    					'password' => '',
    					'email_verified_at' => now()
    			]);
    		}

    		Controller::setCookie(Constant::COOKIE_SCM_TOKEN, $finduser->user_token, 43200); // 30ngay * 24h * 60'
    		Auth::login($finduser);

    		return redirect('/home');
    	} catch (\Exception $e) {
    		return redirect('/login')->with(['domaiNotAuth' => $e->getMessage()]);
    	}

    }

    public function register() {
    	return view('auth.register');
    }
}
