<?php

use Illuminate\Support\Facades\Http;
$year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
$next_year = $year+1;
?>
@extends('admin.layout')
@section('section')
<div class="container-fluid h-screen d-flex flex-column justify-content-center">
    <div class="d-block py-3 px-5 rounded-lg">
        <form action="{{route('admin.students.promotion')}}" method="get" class="w-100 p-2 ">
            @csrf
            <h2 class="my-3 text-dark fw-bolder text-center w-100 text-capitalize">{{__('text.repeat_students')}}</h2>

            <div class="w-100 py-2 text-capitalize">

                <h3 class="py-1 fw-bold text-dark">{{__('text.academic_year')}}</h3>
                <input type="hidden" name="action" value="repeat">
                <div id="section w-100">
                    <div class="form-group py-1 row text-capitalize">
                        <label for="cname" class="text-secondary col-md-3 col-lg-3">{{__('text.word_from')}} </label>
                        <div class="col-md-9 col-lg-9">
                            <select name="year_from" class="form-control text-dark rounded" onchange="set_next_year(event)">
                                <option selected disabled>{{__('text.academic_year')}}</option>
                                @forelse(\App\Models\Batch::all() as $year)
                                <option value="{{$year->id}}" {{\App\Helpers\Helpers::instance()->nextAccademicYear() == $year->id ? 'selected' : ''}}>{{$year->name}}</option>
                                @empty
                                <option>{{__('text.no_sections_created')}}</option>
                                @endforelse
                            </select>
                            <div class="children"></div>
                        </div>
                    </div>
                </div>
                <div id="section w-100">
                    <div class="form-group py-1 row text-capitalize">
                        <label for="cname" class="text-secondary col-md-3 col-lg-3">{{__('text.word_to')}} </label>
                        <div class="col-md-9 col-lg-9">
                            <select name="year_to" class="form-control text-dark rounded section" id="next_year">
                                <option selected disabled>{{__('text.academic_year')}}</option>
                                @forelse(\App\Models\Batch::all() as $year)
                                <option value="{{$year->id}}">{{$year->name}}</option>
                                @empty
                                <option>{{__('text.no_sections_created')}}</option>
                                @endforelse
                            </select>
                            <div class="children"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="w-100 py-2 text-capitalize">

                <h3 class="py-1 fw-bold text-dark">{{__('text.select_class')}}</h3>

                <div id="section w-100">
                    <div class="form-group py-1 row">
                        <label for="cname" class="text-secondary col-md-3 col-lg-3">{{__('text.word_from')}} </label>
                        <div class="col-md-9 col-lg-9">
                            <div id="class-selector">
                                <x-class-filter :data="['field_name'=>'class_from']"></x-class-filter>
                            </div>
                            <div id="next_class_hidden"></div>
                        </div>
                    </div>
                </div>

            </div>
            <div class=" d-flex justify-content-end my-3">
                <a href="{{route('admin.home')}}" class="btn btn-sm btn-danger">{{__('text.word_cancel')}}</a>|
                <button type="submit" class="btn btn-sm btn-primary rounded-lg fw-bold">{{__('text.word_proceed')}}</button>
            </div>
            <input type="hidden" name="" id="workspace">
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
    function set_target(){
        let c_from = $('#class_section').val();
        let html = "";
        html += `<input type="hidden" name="class_to" value="${c_from}">`;
        $('#next_class_hidden').html(html);
    }
    let set_next_year = function(event){
        let year = event.target.value;
        let url = "{{ route('next_year', '__YID__') }}".replace('__YID__', year);
        $.ajax({
            method: 'GET', url: url, success: function(data){
                let html = `
                    <select name="year_to" class="form-control text-dark rounded">
                        <option value="${data.id}" selected>${data.name}</option>
                    </select>
                `;
                $('#next_year').html(html);
            }
        })
    }

    let class_changed = function(element){
        let class_from = $(element).val();
        let nc_field = `<input type="hidden" name="class_to" value="${class_from}">`;
        $('#next_class_hidden').html(nc_field);
    }
</script>
@endsection