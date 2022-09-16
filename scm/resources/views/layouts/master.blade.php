@include('layouts.header')
@if (!Route::is('login') && !Route::is('register'))
@include('layouts.nav')
@endif
<div id="app">
@yield('content')	
</div>
@include('layouts.footer')
