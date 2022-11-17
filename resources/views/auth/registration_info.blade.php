

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
		<link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" />
		<link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" />
		<!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script> -->


		<style>
		    a{
		        text-decoration:none;
		        font-weight:bold;
		        font-size:16px;
		        color:#fff;
		    }
			#frame{
				background-image: url("{{url('bg_image/background_image.'.file_get_contents(base_path('bg_img.text')))}}");
				/* background-color: rgba(250, 250, 250, 0.7); */
				background-position: center;
				background-repeat: no-repeat;
				background-size: cover;
				background-attachment: fixed;
				/* background-blend-mode: overlay; */

			}
		</style>
	</head>

	<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{{route('login')}}">{{config('app.name')}} </a>
    </div>

  </div>
</nav>


	<body class="login-layout" id="frame">
		<div class="main-container">
			<div class="main-content">


				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">


						<div class="login-container">
							<div class="center" style=" padding:5px 10px; ">

								<h4 class="yellow" id="id-company-text" style="color:#ff0">&copy; {{__('text.tech_base')}}</h4>
							</div>

							<div class="space-6"></div>

				  <div class="space-6"></div>
							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger">
												<i class="ace-icon fa fa-coffee green"></i>
											Completing Account Creation
											</h4>
											@if(Session::has('error'))
												<div class="alert alert-danger"><em> {!! session('error') !!}</em>
												</div>
											@endif


											@if(Session::has('e'))
												<div class="alert alert-danger"><em> {!! session('e') !!}</em>
												</div>
											@endif

											@if(Session::has('s'))
												<div class="alert alert-success"><em> {!! session('s') !!}</em>
												</div>
											@endif
											<div class="space-6"></div>

											<form method="post" action="{{ route('createAccount') }}">
											@csrf
												<fieldset>
													<label class="block clearfix"> First Name
														<span class="block input-icon input-icon-right">
															<input type="text" readonly required class="form-control" value="{{ $student_d->name ?? ''}}" name="name" placeholder="First Name" />
															<!--<i class="ace-icon fa fa-user"></i>-->
														</span>
														@error('name')
															<span class="invalid-feedback red" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</label>
													<label class="block clearfix"> Phone Number
														<span class="block input-icon input-icon-right">
															<input type="number" required class="form-control" value="" name="phone" placeholder="Phone Number" />
															<!--<i class="ace-icon fa fa-user"></i>-->
														</span>
														@error('phone')
															<span class="invalid-feedback red" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</label>
														<label class="block clearfix"> E-Mail Address
														<span class="block input-icon input-icon-right">
															<input type="email" required class="form-control" value="" name="email" placeholder="E-Mail Address" />
															<!--<i class="ace-icon fa fa-user"></i>-->
														</span>
														@error('email')
															<span class="invalid-feedback red" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</label>
														<label class="block clearfix"> User Name
														<span class="block input-icon input-icon-right">
															<input type="text" readonly required class="form-control" value="{{ $student_d->matric ?? ''}}" name="username" placeholder="User Name" />
															<!--<i class="ace-icon fa fa-user"></i>-->
														</span>
														@error('username')
															<span class="invalid-feedback red" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</label>
														<label class="block clearfix"> Password
														<span class="block input-icon input-icon-right">
															<input type="password" required class="form-control" value="{{old("password")}}" id="password" name="password" placeholder="Password" />
															<i class="ace-icon fa fa-eye-slash"  id="eye"></i>
														</span>
														@error('password')
															<span class="invalid-feedback red" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</label>
														<label class="block clearfix"> Confirm Password
														<span class="block input-icon input-icon-right">
															<input type="password" required class="form-control" value="{{old("cpassword")}}" id="cpassword" name="cpassword" placeholder="Confirm Password" />
															<i class="ace-icon fa fa-eye-slash"  id="eye1"></i>
														</span>
														@error('cpassword')
															<span class="invalid-feedback red" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</label>
													<div class="space"></div>
													

													<div class="space"></div>

													<div class="clearfix">
														<button type="submit" id="btnSubmit" class="width-35 pull-right btn btn-sm btn-primary">
															<!--<i class="ace-icon fa fa-key"></i>-->
															<!--<span class="bigger-110">{{__('text.word_login')}}</span>-->
															<span class="bigger-110">Register</span>
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>
										</div><!-- /.widget-main -->

										<div class="toolbar clearfix">
										<div>
										
											</div>

											<div>
											
											</div>
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->

								<div id="forgot-box" class="forgot-box widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header red lighter bigger">
												<i class="ace-icon fa fa-key"></i>
												Retrieve Password
											</h4>

											<div class="space-6"></div>
											<p>
												Enter your email where you want to receive instructions from
											</p>

											<form method="POST" action="#">
											@csrf
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="email" required name="email" class="form-control" placeholder="Email" />
															<i class="ace-icon fa fa-envelope"></i>
														</span>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="checkbox" name="type">  Am a student
														</span>
													</label>

													<div class="clearfix">
														<button type="submit" class="width-35 pull-right btn btn-sm btn-danger">
															<i class="ace-icon fa fa-lightbulb-o"></i> Next
														</button>
													</div>
												</fieldset>
											</form>
										</div><!-- /.widget-main -->

										<div class="toolbar center">
											<a href="#" data-target="#login-box" class="back-to-login-link">
												Back to login
												<i class="ace-icon fa fa-arrow-right"></i>
											</a>
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.forgot-box -->


							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->


{{--		@include('inc.student.footer')--}}
				</div>
		<script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script>
	
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='{{asset('assets/js/jquery.mobile.custom.min.js')}}'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
			 $(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible');//hide others
				$(target).addClass('visible');//show target
			 });
			});
		</script>
		<script type="text/javascript">

$("#password").password('toggle');

</script>
	<script src="{{asset('js/jquery.min.js')}}"></script>
 <script type="text/javascript">
    $(function () {
        $("#btnSubmit").click(function () {
            var password = $("#password").val();
            var confirmPassword = $("#cpassword").val();
            if (password != confirmPassword) {
                alert("confirm password do not match.");
                return false;
            }
            return true;
        });
    });
</script>
<script>
   $(function(){
  
  $('#eye').click(function(){
       
        if($(this).hasClass('fa-eye-slash')){
           
          $(this).removeClass('fa-eye-slash');
          
          $(this).addClass('fa-eye');
          
          $('#password').attr('type','text');
            
        }else{
         
          $(this).removeClass('fa-eye');
          
          $(this).addClass('fa-eye-slash');  
          
          $('#password').attr('type','password');
        }
    });
});
$(function(){
  
  $('#eye1').click(function(){
       
        if($(this).hasClass('fa-eye-slash')){
           
          $(this).removeClass('fa-eye-slash');
          
          $(this).addClass('fa-eye');
          
          $('#cpassword').attr('type','text');
            
        }else{
         
          $(this).removeClass('fa-eye');
          
          $(this).addClass('fa-eye-slash');  
          
          $('#cpassword').attr('type','password');
        }
    });
});
</script>
	</body>
</html>
