@extends('layouts.base')

@section('section')
<div class="panel-body">
        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>Day</th>
                    @foreach($periods as $period)                                   
                        <th>{{$period->starts}} Hrs ---- {{$period->ends}} Hrs</th>
                    @endforeach                                                   
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Week_day::get() as $week)
                    <tr>              
                        <td>{{$week->byLocale()->name}}</td>
                            @foreach($periods as $period)
                            
                            <td>@foreach($week->course($student,$year, $semester, $period->id,2) as $course) {{$course->byLocale()->title}},&nbsp;&nbsp;<br>({{$course->course_code}})<br><b>{{--$week->hall->name--}}</b><br><br> @endforeach</td>
                            @endforeach

                         </tr>
                @endforeach
			</tbody>
        </table>
          </div>      		     			
@stop