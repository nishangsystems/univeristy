<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{$title ?? ''}} | {{__('text.app_name')}}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{url('/')}}/public/assets/css/*.css" />

    <link rel="stylesheet" href="{{asset('css/app.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <!-- <link rel="stylesheet" href="{{asset('assets/css/*.css')}}" /> -->
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-part2.min.css')}}" class="ace-main-stylesheet" />
    <![endif]-->
    <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}"/>
    <script src="{{asset('assets/js/ace-extra.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" class="ace-main-stylesheet"
          id="main-ace-style"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/responsive.dataTables.min.css">

    <!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script> -->

    <STYLE>
        body {
            font-family: Arial, Helvetica, sans-serif;
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
        .individual_result_template{
            page-break-after: always;
            page-break-inside: avoid;
        }
        
    </STYLE>
@php
    $bg1 = \App\Http\Controllers\HomeController::getColor('background_color_1');
    $bg2 = \App\Http\Controllers\HomeController::getColor('background_color_2');
    $bg3 = \App\Http\Controllers\HomeController::getColor('background_color_3');
    $header = \App\Helpers\Helpers::instance()->getHeader();
    $year = \App\Models\Batch::find(request('year_id'));
    $semester = \App\Models\Semester::find(request('semester_id'));
    $class = \App\Models\ProgramLevel::find(request('class_id'));
@endphp
</head>
<body class="no-skin">
    <div class="container-fluid bg-light">
        @foreach($students as $student)
            <div class="individual_result_template my-2" style="background-color: white;">
            <table class="table table-bordered table-responsive-md table-striped text-center">
                    <thead>
                        <div class="container-fluid px-0">
                            <img src="{{$header}}" alt="" srcset="" class="img w-100">
                        </div>
                        <div class="container-fluid py-3 h4 my-0 text-center text-uppercase border-top border-bottom border-3 border-dark"><b>
                            {{$semester->name .' '.$year->name. ' '.__('text.individual_results_slip') }}
                        </b></div>
                        <div class="container-fluid p-0 my-0 row mx-auto">
                            <div class="col-sm-7 col-md-8 border-right border-left">
                                <div class="row py-2 border-top border-bottom border-1">
                                    <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_name')}} :</label>
                                    <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$student->name}}</div>
                                </div>
                                <div class="row py-2 border-top border-bottom border-1">
                                    <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_program')}} :</label>
                                    <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$class->program->name}}</div>
                                </div>
                                <div class="row py-2 border-top border-bottom border-1">
                                    <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_matricule')}} :</label>
                                    <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$student->matric}}</div>
                                </div>
                                <div class="row py-2 border-top border-bottom border-1">
                                    <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_level')}} :</label>
                                    <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{ $class->level->level}}</div>
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
                        <tr class="text-uppercase">
                            <th class="text-center" >#</th>
                            <th class="text-center" >{{__('text.word_code')}}</th>
                            <th class="text-center" >{{__('text.word_course')}}</th>
                            <th class="text-center" >ST</th>
                            <th class="text-center" >MV</th>
                            <th class="text-center" >{{__('text.word_module').' / '.$ca_total}}</th>
                            <th class="text-center" >{{__('text.word_module').' / 100'}}</th>
                            <th class="text-center" >{{__('text.word_grade')}}</th>
                            <th class="text-center" >{{__('text.word_remarks')}}</th>
                        </tr>
                    </thead>
                    <tbody class="text-uppercase text-left">
                        @php
                            $k = 1;
                        @endphp
                        @foreach($results->where('student_id', '=', $student->id) as $res)
                            @if (!$res == null)
                            @php
                                $total = $res->ca_score;
                                $grade = $grading->filter(function($ent)use($total){
                                    return $total >= $ent->lower && $total <= $ent->upper;
                                })->first();
                            @endphp
                            <tr class="border-top border-bottom border-secondary border-dashed">
                                <td class="border-left border-right border-light">{{$k++}}</td>
                                <td class="border-left border-right border-light">{{$res->subject->code}}</td>
                                <td class="border-left border-right border-light">{{$res->subject->name}}</td>
                                <td class="border-left border-right border-light">{{$res->class_subject->status ?? $res->subject->status}}</td>
                                <td class="border-left border-right border-light">{{$ca_total}}</td>
                                <td class="border-left border-right border-light">{{$res->ca_score}}</td>
                                <td class="border-left border-right border-light">{{$res->ca_score * 5}}</td>
                                <td class="border-left border-right border-light">{{$grade->grade ?? '----'}}</td>
                                <td class="border-left border-right border-light">{{$rgrade->remark ?? '----'}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr class="border border-secondary text-capitalize h4 fw-bolder">
                            <td colspan="2" class="text-center">{{__('text.total_courses_attempted')}} : <span class="px-3">{{$results->where('student_id', '=', $student->id)->count()}}</span></td>
                            <td colspan="7" class="text-center">{{__('text.total_courses_passed')}} : <span class="px-3">{{$results->where('student_id', '=', $student->id)->where('ca_score', '>=', $ca_total/2)->count()}}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
<script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('assets/vendor/toastr/toastr.min.js') }}"></script>
<script src="{{asset('assets/js/ace.min.js')}}"></script>
<script src="{{ asset('libs')}}/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net-bs4/js/dataTables.responsive.min.js"></script>


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
                                    "{{ $title ?? '' }}",
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
            // order: [
            //     [1, 'asc']
            // ],
        });

    });

    function delete_alert(event, data) {
        event.preventDefault();
        let yes = confirm('You are about to delete an item:'+data+'. This operation can not be reversed. Delete item?');
        if(yes){
            window.location = event.target.href;
        }
    }

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
</html>