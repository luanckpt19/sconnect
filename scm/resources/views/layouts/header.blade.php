<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- CSRF Token -->
    	<meta name="csrf-token" content="{{ csrf_token() }}" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>@yield('title')</title>
        <link rel="icon" type="image/png" sizes="32x32" href="images/logo_sconnect_200.png">

		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome Icons -->
		<link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css"/>
		<!-- icheck bootstrap -->
  		<link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css"/>
  		<!-- toastr -->
  		<link rel="stylesheet" href="/plugins/toastr/toastr.min.css"/>  		
		<!-- Theme style -->
		<link rel="stylesheet" href="/dist/css/adminlte.min.css"/>
		<link rel="stylesheet" href="/css/style.css"/>

		<!-- jQuery -->
		<script src="/plugins/jquery/jquery.min.js"></script>
		<!-- Popper -->
		<script src="/plugins/popper/popper.js"></script>
		<!-- Bootstrap 4 -->
		<script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
		<!-- toastr -->
		<script src="/plugins/toastr/toastr.min.js"></script>
		<!-- AdminLTE App -->
		<script src="/dist/js/adminlte.min.js"></script>
		<script src="/js/font-awesome.js" crossorigin="anonymous"></script>

		<script>
			// Import the functions you need from the SDKs you need
			//import { initializeApp } from "https://www.gstatic.com/firebasejs/9.8.1/firebase-app.js";
			//import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.8.1/firebase-analytics.js";
			// TODO: Add SDKs for Firebase products that you want to use
			// https://firebase.google.com/docs/web/setup#available-libraries

			// Your web app's Firebase configuration
			// For Firebase JS SDK v7.20.0 and later, measurementId is optional
			var firebaseConfig = {
				apiKey: "AIzaSyA0j3I24qFsQBLNGPCW2wH7pWKcT42isxU",
				authDomain: "sconnect-management.firebaseapp.com",
				projectId: "sconnect-management",
				storageBucket: "sconnect-management.appspot.com",
				messagingSenderId: "632977302715",
				appId: "1:632977302715:web:24b673b0f1e0eb3a42359e",
				measurementId: "G-Z4MN5KR1GQ"
			};

		</script>
    </head>
	<body class="hold-transition" style="background-color: #f4f6f9;">