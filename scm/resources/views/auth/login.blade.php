@extends('layouts.master')
@section('content')
<div class="login-page">
<div class="login-box" style="margin-top: -5%">
  	<div class="login-logo">
    	<img src="/images/logo-sconnect-full.png" hspace="10" vspace="10" />
	    <h6>SCONNECT CONTENT MANAGEMENT</h6>
  	</div>
  	<!-- /.login-logo -->
  	<div class="card">
    	<div class="card-body login-card-body">
      		<p class="login-box-msg">Đăng nhập để bắt đầu phiên của bạn</p>
      		<form method="POST" action="{{ route('login') }}">@csrf
	          	@error('email')
				<div class="is-invalid" role="alert"><strong>{{ $message }}</strong></div>
				@enderror
		        <div class="input-group mb-3">
					<input id="email" type="email" 
						class="form-control @error('email') is-invalid @enderror" 
						name="email" value="{{ old('email') }}" required 
						autocomplete="email" autofocus placeholder="Email">                             
		          	<div class="input-group-append">
		            	<div class="input-group-text">
		              		<span class="fas fa-envelope"></span>
		            	</div>
		          	</div>		          	
		        </div>
            	@error('password')
				<div class="is-invalid" role="alert"><strong>{{ $message }}</strong></div>
				@enderror
	        	<div class="input-group mb-3">
	          		<input id="password" type="password" 
	          			class="form-control @error('password') is-invalid @enderror" 
	          			name="password" required autocomplete="current-password" placeholder="Password">						
	          		<div class="input-group-append">
	            		<div class="input-group-text">
	              			<span class="fas fa-lock"></span>
		            	</div>		            	
		          	</div>
        		</div>        
		        <div class="row">
					<div class="col-8">
		            	<div class="icheck-primary">
		              		<input type="checkbox" id="remember">
		              		<label for="remember" style="font-weight: normal;important">Remember Me</label>
		            	</div>
		          	</div>
		          	<!-- /.col -->
		          	<div class="col-4">
		            	<button type="submit" class="btn btn-primary btn-block">Sign In</button>
		          	</div>
		          	<!-- /.col -->
				</div>
			</form>

			<div class="social-auth-links text-center mb-3" style="padding-top: 30px;">
				<p>- OR -</p>
				@php
					$googleUser = null;
					try {                               	                                
						$scmToken = App\Http\Controllers\Controller::getCookie(App\Constant::COOKIE_SCM_TOKEN);
						$googleUser = Socialite::driver('google')->userFromToken($scmToken);
					} catch (\Exception $e) {}
					if ($googleUser) { @endphp
					<a href="{{ route('login.google') }}" class="btn btn-block btn-danger"><i class="fab fa-google-plus mr-2"></i> {{ __('Continue with ' . $googleUser->name) }}</a>
					<div style="padding-top: 5px;"><a href="{{ route('login.google.prompt') }}">Login with other account</a></div>
					@php } else { @endphp
					<a href="{{ route('login.google.prompt') }}" class="btn btn-block btn-danger"><i class="fab fa-google-plus mr-2"></i> {{ __('Sign in using Google') }}</a>
					@php
					}
				@endphp
				@if (\Session::has('domaiNotAuth'))
					<div style="padding-top: 5px;" class="is-invalid"><small>{!! \Session::get('domaiNotAuth') !!}</small></div>
				@endif
			</div>
			<!-- /.social-auth-links -->

			@if (Route::has('password.request'))
			<p class="mb-1">				
				<a class="btn btn-link" href="{{ route('password.request') }}">
				{{ __('I forgot my password') }}
				</a>				
			</p>
			@endif
		</div>
		<!-- /.login-card-body -->
	</div>
</div>
<!-- /.login-box -->
</div>

@endsection
