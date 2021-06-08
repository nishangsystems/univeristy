@extends('layouts.base')

@section('section')
<div class="panel-body">
<h3 class="header smaller lighter blue">{{$title}}</h3>
<table style="width:100%;" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Matricule</th>
                        <th>CA Mark</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $count = 1; ?>
                  @foreach($students as $student)
                  <tr>
                    <input type="hidden" name="ids[]"  value="{{\App\Students::find($student->s_id)->studentInfo->id}}">
                        <td>{{$count}}</td>
                        <td>{{$student->firstname}}</td>
                        <td>{{$student->matric}}</td>
                        <?php 
                        $student = \App\Students::find($student->s_id);
                        $result = $student->studentInfo->resultModel($year,$semester)->where('course_id','=',$course)->first(); ?>
                        <td>{{$result?$result->ca:''}}</td>                     
                    </tr>
                    <?php $count++; ?>
                  @endforeach
                </tbody>     
            </table>
          </div>      		     			
@stop