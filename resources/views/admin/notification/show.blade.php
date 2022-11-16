@extends('admin.layout')

@section('section')
               
          <div class="col-lg-12 m-4 box-shadow pt-4">
         
             <h3 class="p-4">{!! $notification->title !!}  </h3>
             <p class="p-4"> Created by : {{$notification->created_by()->first()->name ?? ''}} </p>
             <p class="p-4"> Program    : {{($notification->program == null)?"For all Programs":$notification->program->name}} </p>
             <p class="p-4"> Level      : {{($notification->level == null)?"For all Levels":$notification->level->name}} </p>
             <p class="p-4"> Date       : {{ $notification->created_at->diffForHumans() }}</p>
              <div class="m-4 p-4">
                  {!! $notification->message!!}      
            </div>
@stop