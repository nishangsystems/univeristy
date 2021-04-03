<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" href="{{url('public')}}/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{url('public')}}/assets/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="{{url('public')}}/assets/css/fonts.googleapis.com.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- ace styles -->
    <link rel="stylesheet" href="{{url('public')}}/assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="{{url('public')}}/assets/css/ace-skins.min.css" />
    <link rel="stylesheet" href="{{url('public')}}/assets/css/ace-rtl.min.css" />
    <link rel="stylesheet" href="{{ url('public/assets/vendor/toastr/toastr.min.css') }}">
    <script src="{{url('public')}}/assets/js/ace-extra.min.js"></script>
    <link rel="stylesheet" href="{{url('public')}}/assets/css/custom.css" class="ace-main-stylesheet" id="main-ace-style" />
    <style>
        body{
            font-family:Arial, Helvetica, sans-serif
        }
    </style>
</head>

<body class="no-skin">

@include('inc.student.header1')
<div class="main-container ace-save-state" id="main-container">

    @include('inc.student.nav')
    <div class="main-content">
        <div class="main-content-inner">
            @include('inc.student.header2')

            @if(Session::has('s'))
                <div class="alert alert-success fade in">
                    <strong>Success!</strong> {{Session::get('s')}}
                </div>
            @endif

            @if(Session::has('e'))
                <div class="alert alert-danger fade in">
                    <strong>Error!</strong> {{Session::get('e')}}
                </div>
            @endif

            @if(Session::has('errors'))
                <div class="alert alert-danger fade in">
                    <strong>Error!</strong>{{Session::get('errors')}}
                </div>
            @endif
            <div class="page-content">
                @yield('section')
            </div>
        </div>
    </div>
</div>


@include('inc.student.footer')
</div>

<script src="{{url('public')}}/assets/js/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='{{url('public')}}/assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>
<script src="{{url('public')}}/assets/js/bootstrap.min.js"></script>
<script src="{{url('public')}}/assets/js/ace.min.js"></script>
<script src="{{ url('public/assets/vendor/toastr/toastr.min.js') }}"></script>
@yield('script')
</body>
</html>
