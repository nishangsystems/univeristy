<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{config('app.name')}}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{url('css/app.css')}}" />
    <link rel="stylesheet" href="{{url('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{url('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{url('assets/css/fonts.googleapis.com.css')}}" />
    <link rel="stylesheet" href="{{url('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="{{url('assets/css/ace-part2.min.css')}}" class="ace-main-stylesheet" />
    <link rel="stylesheet" href="{{url('assets/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{url('assets/css/ace-rtl.min.css')}}"/>
    <script src="{{url('assets/js/ace-extra.min.js')}}"></script>
    <link rel="stylesheet" href="{{url('assets/css/custom.css')}}" class="ace-main-stylesheet"
          id="main-ace-style"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/responsive.dataTables.min.css">

    <STYLE>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: url("{{url('/assets/images/logo.jpeg')}}");
            background-color: rgba(255, 255, 255, 0.95);
            background-position: center;
            background-size: contain;
            background-repeat: no-repeat;
            background-attachment: local;
            background-blend-mode: overlay;
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
        table th, table td{
            padding: 10px;
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
    </STYLE>
</head>
<body>
        @php
            $program_name = \App\Models\ProgramLevel::find(auth()->user()->program_id)->program()->first()->name;
            $faculty = \App\Models\ProgramLevel::find(auth()->user()->program_id)->program()->first()->parent->parent;
            $current_year_name = \App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name;
            $current_semester = \App\Helpers\Helpers::instance()->getSemester(auth()->user()->program_id)->id;
            $current_semester_name = \App\Helpers\Helpers::instance()->getSemester(auth()->user()->program_id)->name;
            $flag = true;
        @endphp
        <div class="card-body">
        <div id="table table-responsive" class="table-editable">

            <table class="table table-bordered table-responsive-md table-striped text-center" id="content-box">
                <thead>
                    <div class="container-fluid px-0">
                        <img src="{{url('/assets/images/header.jpg')}}" alt="" srcset="" class="img w-100">
                    </div>
                    <div class="container-fluid py-3 h4 my-0 text-center text-uppercase border-top border-bottom border-3 border-dark"><b>
                        {{\App\Helpers\Helpers::instance()->getSemester(auth()->user()->program_id)->name .' '.\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name. ' '.__('text.CA') }}
                    </b></div>
                    <div class="container-fluid p-0 my-0 row mx-auto">
                        <div class="col-sm-7 col-md-8 border-right border-left">
                            <div class="row py-2 border-top border-bottom border-1">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_name')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$user->name}}</div>
                            </div>
                            <div class="row py-2 border-top border-bottom border-1">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_faculty')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$faculty->name}}</div>
                            </div>
                            <div class="row py-2 border-top border-bottom border-1">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_program')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{\App\Models\ProgramLevel::find($user->program_id)->program->name}}</div>
                            </div>
                            <div class="row py-2 border-top border-bottom border-1">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_matricule')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$user->matric}}</div>
                            </div>
                            <div class="row py-2 border-top border-bottom border-1">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_level')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{ \App\Models\ProgramLevel::find($user->program_id)->level->level}}</div>
                            </div>
                        </div>
                        <div class="col-sm-5 col-md-4 border">
                            @foreach($grading as $grd)
                                <span class="d-flex flex-wrap py-2 fs-3">
                                    <span class="col-2">{{($grd->grade ?? '')}}</span>
                                    <span class="col-2">{{':'.($grd->lower ?? '')}}</span>
                                    <span class="col-3">{{'-'.($grd->upper ?? '').'%  '}}</span>
                                    <span class="col-2">{{($grd->weight ?? '')}}</span>
                                    <span class="col-3">{{'  '.($grd->remark ?? '')}}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <tr class="text-capitalize">
                        <th class="text-center" >{{__('text.word_code')}}</th>
                        <th class="text-center" >{{__('text.word_course')}}</th>
                        <th class="text-center" >ST</th>
                        <th class="text-center" >CV</th>
                        <th class="text-center" >{{__('text.CA').'/'.$ca_total}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $subject)
                        <tr class="border-top border-bottom border-secondary border-dashed">
                            <td class="border-left border-right border-light">{{$subject['code']}}</td>
                            <td class="border-left border-right border-light">{{$subject['name']}}</td>
                            <td class="border-left border-right border-light">{{$subject['status']}}</td>
                            <td class="border-left border-right border-light">{{$subject['coef']}}</td>
                            <td class="border-left border-right border-light">{{$subject['ca_mark']}}</td>
                        </tr>
                    @endforeach
                    <tr class="border border-secondary text-capitalize h4 fw-bolder">
                        <td colspan="2" class="text-center">{{__('text.total_courses_attempted')}} : <span class="px-3">{{count($results)}}</span></td>
                        <td colspan="7" class="text-center"></td>
                    </tr>
                </tbody>
            </table>
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
                                    '{{ $title ?? "" }}',
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
