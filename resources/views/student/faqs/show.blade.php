@extends('student.layout')

@section('section')
               
          <div class="col-lg-12 m-4 box-shadow pt-4">
         
            <h3 class="p-4">{!! $item->question !!}  </h3>
            <div class="m-4 p-4">
                  {!! $item->answer !!}      
            </div>
          </div>
@stop