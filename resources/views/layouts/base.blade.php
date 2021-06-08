
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>{{config('app.name')}}</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="{{url('public')}}/assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="{{url('public')}}/assets/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="{{url('public')}}/assets/css/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="{{url('public')}}/assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="{{url('public')}}/assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="{{url('public')}}/assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="{{url('public')}}/assets/css/ace-rtl.min.css" />
		<script src="{{url('public')}}/assets/js/ace-extra.min.js"></script>
	</head>

	<body class="no-skin">
	
		<div class="main-container ace-save-state" id="main-container">
        
			<div class="main-content">
                <div class="main-content-inner">
			
                @yield('section')
            </div>
		</div>
		</div>

			
	
        </div>
        
		<script src="{{url('public')}}/assets/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="{{url('public')}}/assets/js/bootstrap.min.js"></script>
		<script src="{{url('public')}}/assets/js/ace.min.js"></script>
		@yield('script')
	</body>
</html>
