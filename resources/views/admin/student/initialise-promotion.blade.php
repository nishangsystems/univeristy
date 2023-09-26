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
            <h2 class="my-3 text-dark fw-bolder text-center w-100 text-capitalize">{{__('text.word_promotion')}}</h2>
            <i><h4 class="text-uppercase text-center">_______{{__('text.word_from')}}: <span class="text-primary">{{\App\Models\Batch::find($year)->name}}</span> _______ {{__('text.word_to')}}: <span class="text-primary">{{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->nextAccademicYear())->name ?? '______'}}</span>_______</h4></i>

            <div class="w-100 py-2 text-capitalize">

                <h3 class="py-1 fw-bold text-dark">{{__('text.academic_year')}}</h3>

                <div id="section w-100">
                    <div class="form-group py-1 row">
                        <label for="cname" class="text-secondary col-md-3 col-lg-3">{{__('text.word_from')}} </label>
                        <div class="col-md-9 col-lg-9">
                            <input type="hidden" name="year_from" value="{{$year}}">
                            <span class="form-control">{{\App\Models\Batch::find($year)->name}}</span>
                            <div class="children"></div>
                        </div>
                    </div>
                </div>
                <div id="section w-100">
                    <div class="form-group py-1 row text-capitalize">
                        <label for="cname" class="text-secondary col-md-3 col-lg-3">{{__('text.word_to')}} </label>
                        <div class="col-md-9 col-lg-9">
                            <select name="year_to" class="form-control text-dark rounded section" id="section" onchange="set_target()">
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

            </div>

            <div class="w-100 py-2 text-capitalize">

                <h3 class="py-1 fw-bold text-dark">{{__('text.select_class')}}</h3>

                <div id="section w-100">
                    <div class="form-group py-1 row">
                        <label for="cname" class="text-secondary col-md-3 col-lg-3">{{__('text.word_from')}} </label>
                        <div class="col-md-9 col-lg-9">
                            <select name="class_from" class="form-control text-dark rounded section text-capitalize" id="class_section" oninput="set_target()">
                                <option selected disabled>{{__('text.select_section')}}</option>
                                @forelse(\App\Http\Controllers\Controller::sorted_program_levels() as $class)
                                <option value="{{$class['id']}}">{{$class['name']}}</option>
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
                        <div class="col-md-9 col-lg-9" id="nex_class_section">
                            <select name="class_to" class="form-control text-dark rounded section text-uppercase">
                                <option disabled>{{__('text.select_target_section')}}</option>
                            </select>
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
        let url = "{{ route('promotion.class.target', '__CLASSID__') }}".replace('__CLASSID__', c_from);
        // console.log(url);
        $.ajax({
            method: 'get',
            url: url,
            success: function(data){
                // console.log(data);
                let html = "";
                html += `<select name="class_to" class="form-control text-dark rounded section text-uppercase">
                        <option disabled>{{__('text.select_target_section')}}</option>`;
                data.classes.forEach(_class => {
                    html += `<option value="`+_class.id+`">`+_class.name+`</option>`
                });
                html += `</select>`;
                $('#nex_class_section').html(html);
            }
        });
    }
</script>
@endsection