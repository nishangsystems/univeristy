<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{config('app.name')}}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{asset('css/app.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-part2.min.css')}}" class="ace-main-stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}"/>
    <script src="{{asset('assets/js/ace-extra.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" class="ace-main-stylesheet"
          id="main-ace-style"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/responsive.dataTables.min.css">

    <STYLE>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #FFF;
        }
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            width: 100%;
        }
        .dt-button{
            background-image: none!important;
            border: 1px solid #FFF;
            border-radius: 0;
            padding: 5px 20px;
            border-radius: 5px;
            box-shadow: none!important;
            -webkit-transition: background-color .15s,border-color .15s,opacity .15s;
            -o-transition: background-color .15s,border-color .15s,opacity .15s;
            transition: background-color .15s,border-color .15s,opacity .15s;
            vertical-align: middle;
            margin: 0;
            position: relative;
        }
        table{padding: 0px !important}
        table th{
            padding: 6px;
        }
        table td{
            padding: 4px;
        }
        .table td{
            border-bottom: 1px  solid  #f1f1f1 !important;
        }
        .nav li {
            display: block;
            width: 100% !important;
        }
        .dropdown-toggle:after {
            display: none;
        }
        #table_____{
            font-weight: 700;
            background-image: url("{{url('/assets/images/logo.jpeg')}}");
            background-color: rgba(255, 255, 255, 0.8);
            background-position: center;
            background-size: contain;
            background-repeat: no-repeat;
            background-attachment: local;
            background-blend-mode: overlay;
        }
    </STYLE>
</head>
<body>
    @php
        $program_name = \App\Models\ProgramLevel::find(auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->id)->program()->first()->name;
        $faculty = \App\Models\ProgramLevel::find(auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->id)->program()->first()->parent->parent;
        $current_year_name = \App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
        $current_semester = \App\Helpers\Helpers::instance()->getSemester(auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->id)->id;
        $current_semester_name = \App\Helpers\Helpers::instance()->getSemester(auth('student')->user()->_class(\App\Helpers\Helpers::instance()->getYear())->id)->name;
        $flag = true;
    @endphp
            <div class="" id="table_____">
                
                
                <div class=""  style="background-color: rgba(255, 255, 255, 0.9);">
                    <div class="" >
                        <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" class="w-100 h-auto" alt="">
                        <div class="border-top border-dashed my-2 " style="display: flex;">
                            <div class="" style="flex: 1;">
                                <div class="py-1 h4 row">
                                    <span class="col-md-3 text-capitalize">{{__('text.word_name')}}:</span>
                                    <span class="col-md-9 pl-2 text-uppercase">{{$user->name}}</span>
                                </div>
                                <div class="py-1 h4 row">
                                    <span class="col-md-3 text-capitalize">{{__('text.word_matricule')}}:</span>
                                    <span class="col-md-9 pl-2 text-uppercase">{{$user->matric}}</span>
                                </div>
                            </div>
                            <div class="" style="flex: 1;">
                                <div class="py-1 h4 row">
                                    <span class="col-md-3 text-capitalize">{{__('text.word_program')}}:</span>
                                    <span class="col-md-9 pl-2 text-uppercase">{{$program_name}}</span>
                                </div>
                                <div class="py-1 h4 row">
                                    <span class="col-md-3 text-capitalize">{{__('text.word_faculty')}}:</span>
                                    <span class="col-md-9 pl-2 text-uppercase">{{$faculty->name}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center h4 text-uppercase" style="font-weight: 700;">{{trans('text.formb_title2', ['year'=>$current_year_name, 'semester'=>$current_semester_name])}}</div>
                    </div>
                    <table class="">
                        <thead class="text-capitalize bg-light">
                            <th class="border-left border-right">{{__('text.course_code')}}</th>
                            <th class="border-left border-right">{{__('text.course_title')}}</th>
                            <th class="border-left border-right">{{__('text.credit_value')}}</th>
                            <th class="border-left border-right">{{__('text.word_status')}}</th>
                        </thead>
                        <tbody id="course_table">
    
                            @foreach($courses as $course)
                                <tr class="border-bottom border-light {{$flag ? 'bg-light' : ''}}">
                                    <td class="border-left border-right">{{$course->code}}</td>
                                    <td class="border-left border-right">{{$course->name}}</td>
                                    <td class="border-left border-right">{{$course->cv}}</td>
                                    <td class="border-left border-right">{{$course->status}}</td>
                                </tr>
                                @php($flag = !$flag)
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex py-3 py-md-1 justify-content-center">
                        <div class="border-top px-6 fw-bolder h5 text-capitalize">{{__('text.total_credit_attempted')}} : <span id="cv-sum">{{$cv_sum}}</span></div>
                    </div>
                    <div class="d-flex py-3 py-md-1 justify-content-end">
                        <div class="border-top px-6 fw-bolder h5 text-capitalize" style="float: right;">{{__('text.the_dean')}} <br> <span id="cv-sum">__________________________</span></div>
                    </div>
                </div>
            </div>

    <script>
    $(function () {
        $('.table , .adv-table table').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel',
                {
                    text: 'Download PDF',
                    extend: 'pdfHtml5',
                    message: '',
                    orientation: 'portrait',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.pageMargins = [10,10,10,10];
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 7;
                        doc.styles.title.fontSize = 9;
                        doc.content[0].text = doc.content[0].text.trim();
    
                        doc['footer']=(function(page, pages) {
                            return {
                                columns: [
                                    '{{ $title ?? '' }}',
                                    {
                                        // This is the right column
                                        alignment: 'right',
                                        text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                    }
                                ],
                                margin: [10, 0]
                            }
                        });
                        // Styling the table: create style object
                        var objLayout = {};
                        // Horizontal line thickness
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        // Vertikal line thickness
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        // Horizontal line color
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        // Vertical line color
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        // Left padding of the cell
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        // Right padding of the cell
                        objLayout['paddingRight'] = function(i) { return 4; };
                        // Inject the object in the document
                        doc.content[1].layout = objLayout;
                    }
                }
    
            ],
            info:     false,
            searching: true,
            ordering : false
        });
    
    });
    
    
    </script>
    
    <script src="{{ asset('libs')}}/datatables.net/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/jszip.min.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/pdfmake.min.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/vfs_fonts.js"></script>
    <script src="{{ asset('libs')}}/datatables.net/js/buttons.html5.min.js"></script>
    
    <script>
    (function($){
        'use strict';
        $(window).on('load', function () {
            if ($(".pre-loader").length > 0)
            {
                $(".pre-loader").fadeOut("slow");
            }
        });
    })(jQuery)
    </script>
</body>

