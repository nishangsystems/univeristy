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
            border: none;
            width: 100%;
            margin-top: 10px;
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
            max-width: 296mm !important;
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
            padding: 3px;
            font-size: 9px;
            border-right: 1px solid  #888888;
        }

        .score{
            width: 35px;
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
@endphp
@foreach($results as $batch)
    <div class="page landscape">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr style="height: 30px">
                <td colspan="5" rowspan="2">
                    <p style="font-weight: bold; font-size: 11px;" class="mt-0"> ST LOUIS UNIVERSITY INSTITUTE<BR>
                    <p style="font-size:11px; font-weight:bold">Medical Studies, Engineering & Technology ,
                        Agriculture</p>
                    <div class="font-10">
                        22-02 902/L/MINESUP/DDES/SD-ESUP/SDA/ANAP OF MAY,2022
                        <br>P.O BOX 77 BONABERI-DOUALA
                        <br>REPUBLIC OF CAMEROON
                    </div>

                </td>
                <td colspan="4" rowspan="2" class="font-10"> STUDENTS ACADEMIC RECORDS</td>
                <td colspan="3" class="font-10"> Student's No: <span class="font-10 bold">{{$student->matric}}</span>
                </td>
                <td colspan="3" class="font-10 text-center">Date
                    Printed: {{\Carbon\Carbon::now()->format("d-m-Y")}}</td>
                <td colspan="3" class="font-10 text-center">Page <br> {{$page}} OF {{count($results)}}</td>
            </tr>
            <tr style="height: 30px">
                <td colspan="4" style="border-right-color: transparent;" class="font-10">First Name:
                    <b>{{explode(' ',$student->name)[0]}}</b></td>
                <td colspan="5" class="font-10">Other Name(s): <b>{{ substr(strstr($student->name," "), 1)}}</b></td>
            </tr>
            @if($page == 1)
                <tr>
                    <td colspan="5" style="font-weight: bold">Date of Birth:<br/>{{$student->dob}}</td>
                    <td colspan="4" style="font-weight: bold">Place of Birth:<br/>{{$student->pob}}</td>
                    <td colspan="3" style="font-weight: bold">Sex: <br/>{{$student->gender}}</td>
                    <td colspan="6" style="font-weight: bold">This Transcript is not valid without the signature of
                        the Registrar and Enbossed seal of the school
                    </td>
                </tr>
                <tr>
                    <td colspan="12" style="font-weight: bold">Date of Enrolment : {{$student->batch->name}}</td>
                    <td colspan="6" rowspan="2" style="font-weight: bold">
                        @foreach($gradings as $grade)
                            <div style="display: flex;">
                                <div  class="grade">{{$grade->grade}}</div>
                                <div  class="grade">{{$grade->lower}}</div>
                                <div  class="grade">-</div>
                                <div  class="grade">{{$grade->upper}}</div>
                                <div  class="grade">{{$grade->weight}} GP</div>
                            </div>
                        @endforeach

                    </td>
                </tr>
                <tr>
                    <td colspan="12" style="font-weight: bold">
                        @php
                            $program = \App\Models\ProgramLevel::find($student->_class()->id)->program;
                        @endphp
                        <div> Department : {{$program->name}}</div>
                        <div> Degree Conferred :</div>
                        <div> Date :</div>
                    </td>
                </tr>

            @endif

            @component('livewire.admin.transcript.year',['batch'=>$batch,'isLast'=>($page == count($results)),
                'tca'=>$totalCreditAttempted,
                'tce'=>$totalCreditEarned,
                'tgpa'=>$gpa]
            )@endcomponent
            </tbody>
        </table>
    </div>
    @php
        $page ++;
    @endphp
@endforeach
</body>
</html>