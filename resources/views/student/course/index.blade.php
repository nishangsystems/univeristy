@extends('student.layout')

@section('section')




<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle">
                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                    &nbsp;{{$title}}
                </a>

                      
            </h4>

            
        </div>

	<div class="panel-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Credit Value</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php $counter = 1; ?>
                    @foreach($courses as $course)
                    <tr>   
                        <td>{{$counter}}</td>
                        <td>{{$course->code}}</td>
                        <td>{{$course->name}}</td>
                        <td>{{$course->coef}}</td>
                        <td>{{$course->status}}</td>
                        <td>
                            <a href="" class=" btn btn-danger btn-xs m-2">Materials</a>
                            <a href="" class=" btn btn-success btn-xs m-2">Assignment</a>
                        </td>
                    </tr>
                    <?php $counter = $counter + 1; ?>
                    @endforeach
                
                </tbody>
            </table>
           
        </div>
     </div>
 <div>
@stop