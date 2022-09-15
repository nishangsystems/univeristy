<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>{{config('app.name')}}</title>

		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
		<!-- <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" /> -->
		<!-- <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" /> -->
		<!-- <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" /> -->

		<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<style>
		    a{
		        text-decoration:none;
		        font-weight:bold;
		        font-size:16px;
		        color:#fff;
		    }
		</style>
	</head>

	<nav class="navbar navbar-inverse rounded-0 bg-primary">
		<div class="container-fluid">
			<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">{{__('text.app_name')}} </a>
			</div>

		</div>
	</nav>


	<body>
		<div class="d-flex justify-content-center bg-dark w-100">
			<form action="{{url('register')}}" method="post" class="form px-3 my-5 py-5 col-sm-10 col-md-8 mx-auto bg-light rounded-2">
				<div class="text-capitalize h3 fw-bold text-dark py-2 mb-3 text-center">User Registration</div>
				@csrf
				<div class="form-group my-3">
					<div class="input-group-prepend col-4"><span class="input-group-text col-4 rounded-0">username:</span></div>
					<input type="text" name="username" class="form-control rounded-0" placeholder="please enter your username">
				</div>
				<div class="form-group my-3">
					<div class="input-group-prepend col-4"><span class="input-group-text col-4 rounded-0">email:</span></div>
					<input type="email" name="email" class="form-control rounded-0" placeholder="please enter your email">
				</div>
				<div class="form-group my-3">
					<div class="input-group-prepend col-4"><span class="input-group-text col-4 rounded-0">phone:</span></div>
					<input type="tel" name="phone" class="form-control rounded-0" placeholder="please enter your phone number">
				</div>
				<div class="form-group my-3">
					<div class="input-group-prepend col-4"><span class="input-group-text col-4 rounded-0">address:</span></div>
					<input type="text" name="address" class="form-control rounded-0" placeholder="please enter your address">
				</div>
				<div class="form-group my-3">
					<div class="input-group-prepend col-4"><span class="input-group-text col-4 rounded-0">gender:</span></div>
					<select name="gender" class="form-control" id="">
						<option value="">select gender</option>
						<option value="male">Male</option>
						<option value="female">Female</option>
					</select>
				</div>
				
				<input type="hidden" name="type" value="admin">
				<input type="hidden" name="campus_id" value="1">
				<input type="hidden" name="school_id" value="1">

				<div class="d-flex justify-content-end my-3 py-2 form-group"><input type="submit" name="" id="" value="create" class="rounded btn px-3 py-1"></div>
			</form>
		</div>

	</body>
</html>
