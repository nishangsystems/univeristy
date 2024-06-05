<html>
<head>
    <title>{{ $student->name}}</title>
    <style>
        body {
            background: #fff;
            font-size: 12px !important;
            line-height: 2;
            font-family: Arial, Helvetica, sans-serif;
        }

        page {
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
        }


        div.landscape {
            margin: auto;
            overflow: hidden;
            page-break-after: always;
            background: white;
        }

        table {
            border-collapse: collapse;
            max-width: 280mm !important;
            border: none;
            width: 100%;
            margin:10px 20px;
            font-size: 10px !important;
            border-spacing: 0;
        }

        p {
            display: block;
            margin-block-start: 0.5em;
            margin-block-end: 0.5em;
        }

        .font-10 {
            font-size: 10px;
        }

        .border-right{
            border-right: 1px solid #888888;
        }

        .font-13 {
            font-size: 13px;
        }

        .font-12 {
            font-size: 12px;
        }

        .text-center {
            text-align: center;
        }

        .mt-0 {
            margin: 0px;
        }

        .bold {
            font-weight: bold;
        }

        td {
            border: 1px solid  #888888;
        }

        td {
            padding: 2px 5px;
        }

        div.landscape {
            max-width: 286mm !important;
            max-height: 207mm;
        }

        @media print {
            body {
                background: none;
                -ms-zoom: 1.665;
            }

            div.landscape {
                margin: 0;
                padding: 0;
                border: none;
                background: none;
            }
        }

        .grade {
            font-size: 10px;
            width: 40px;
            text-align: start;
        }

        .title{
            width: 52px;
            padding: 2px 3px;
            font-size: 9px;
            border-right: 1px solid  #888888;
        }

        .score{
            width: 23px;
            padding: 2px 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            border-right: 1px solid  #888888;
            text-align: center;
        }

        .border-right-2{
            border-right: 1px solid  #888888;
        }

        .border-none{
            border-right: none;
        }

        .text-left{
            text-align: left !important;
            flex-grow: 1;
        }
    </style>
</head>
<body>
@php
    $page = 1;
    $school = \App\Models\School::first();
@endphp
@foreach($results as $batch)
    <div class="page landscape">
        <table border="0" cellspacing="0" cellpadding="0" style="height: {{$page == count($results)?"180mm;":"190mm;"}}">
            <tbody>
            <tr style="height: 30px">
                <td colspan="6" rowspan="2" width="34%">
                    <p style="font-weight: bold; font-size: 11px;" class="mt-0">
                        {!! $school->name !!}
                    </p>

                    <div class="font-10">
                      {!! $school->address !!}
                    </div>

                </td>
                <td colspan="3" width="17%" rowspan="2" class="font-10"> STUDENTS ACADEMIC RECORDS</td>
                <td colspan="4" width="22%" class="font-10"> Student's No: <span class="font-10 bold">{{$student->matric}}</span>
                </td>
                <td colspan="3" class="font-10 text-center">
                    Date Printed <br>
                {{\Carbon\Carbon::now()->format("d-m-Y")}}
                </td>
                <td colspan="2" class="font-10 text-center">Page <br> {{$page}} OF {{count($results)}}</td>
            </tr>
            <tr style="height: 30px">
                <td colspan="5" style="border-right-color: transparent;" class="font-10">First Name:
                    <b>{{explode(' ',$student->name)[0]}}</b></td>
                <td colspan="4" class="font-10">Other Name(s): <b>{{ substr(strstr($student->name," "), 1)}}</b></td>
            </tr>
            @if($page == 1)
                <tr style="height: 30px">
                    <td colspan="5" style="font-weight: bold">Date of Birth:<br/>{{\Carbon\Carbon::parse($student->dob)->format('d-m-Y')}}</td>
                    <td colspan="4" width="22%" style="font-weight: bold">Place of Birth:<br/>{{$student->pob}}</td>
                    <td colspan="4" width="22%" style="font-weight: bold">Sex: <br/>{{$student->gender}}</td>
                    <td colspan="5" width="28%" style="font-weight: bold">This Transcript is not valid without the signature of
                        the Registrar and Enbossed seal of the school
                    </td>
                </tr>
                <tr>
                    <td colspan="13" style="font-weight: bold; height: 30px; padding: 0px 5px;">Date of Enrolment : {{$student->batch->name}}</td>
                    <td colspan="5" rowspan="2" style="height:90px; font-weight: bold; padding: 0px 5px">
                            @foreach($gradings as $grade)
                                <div style="display: flex;">
                                    <div  class="grade">{{$grade->grade}}</div>
                                    <div  class="grade">{{$grade->lower}}</div>
                                    <div  class="grade">-</div>
                                    <div  class="grade">{{$grade->upper}}</div>
                                    <div  class="grade">{{$grade->weight}} GP</div>
                                </div>
                            @endforeach
                            <div style="display: flex;">
                                <div  class="grade">C-</div>
                                <div>Compulsory</div>
                            </div>
                            <div style="display: flex;">
                                <div  class="grade">R-</div>
                                <div>School Requirement</div>
                            </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="13" style="font-weight: bold;  height: 60px; padding: 0px 5px; ">
                       <div style="display: flex; flex-direction: column; justify-content: space-around; height: 100%;">
                           @php
                               $program = \App\Models\ProgramLevel::find($student->_class()->id)->program;
                           @endphp
                           <div style="display: flex">
                               <span style="width: 120px;">Department :</span>
                               <span>{{$program->name}}</span>
                           </div>

                           <div style="display: flex">
                               <span style="width: 120px">Degree Proposed :</span>
                               <span>{{$program->deg_name}}</span>
                           </div>
                           <div style="display: flex">
                               <span style="width: 120px">Degree Conferred :</span>
                                @if($totalCreditEarned >= $program->max_credit)
                                   <span>{{$program->deg_name}}</span>
                                @endif
                           </div>
                           <div style="display: flex">
                               <span style="width: 120px">Date :</span>
                               @if($totalCreditEarned >= $program->max_credit)
                                   <span>{{explode('/',collect($results)->last()[0]['year_name'])[1] }}</span>
                               @endif
                           </div>
                       </div>
                    </td>
                </tr>

            @endif

            @component('livewire.admin.transcript.year',['batch'=>$batch,'isLast'=>($page == count($results)),
                'tca'=>$totalCreditAttempted,
                'tce'=>$totalCreditEarned,
                'tgpa'=>$gpa
                ]
            )@endcomponent

            </tbody>
        </table>
        @if($page == count($results))
            <div style="display: flex; justify-content: center; font-weight: bold">Registrar</div>
        @endif
    </div>
    @php
        $page ++;
    @endphp
@endforeach
</body>
</html>