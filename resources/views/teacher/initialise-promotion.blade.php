<?php

use Illuminate\Support\Facades\Http;

?>
@extends('teacher.layout')
@section('section')
<div class="container-fluid h-screen d-flex flex-column justify-content-center">
    <div class="w-75 mx-auto d-block py-3 px-5 rounded-lg">
        <form action="{{route('user.students.promotion')}}" method="get" class="w-100 p-2 ">
            @csrf
            <h2 class="my-3 text-dark fw-bolder text-center w-100">Student Promotion</h2>
            <div class="w-100 py-2 d-md-flex">
                <div class="w-50 px-4">
                    <h3 class="py-1 fw-bold text-dark">Academic Year</h3>
                    <div class="form-group w-100 py-1">
                        <label for="year_from" class="text-secondary">From:</label>
                        @php($xyz = \App\Models\ClassMaster::where('user_id', auth()->user()->id)->get('batch_id'))
                        <select name="year_from" id="" class="form-control text-dark rounded" onchange="set_next_year()" required>
                            <option value="{{$xyz->first()->batch_id}}" selected>{{\App\Models\Batch::find($xyz->first()->batch_id)->name}}</option>
                            <option value="" disabled>select a year</option>
                        </select>
                    </div>
                    <div class="form-group w-100 py-1">
                        <label for="year_to" class="text-secondary">To:</label>
                        <!-- <input type="text" name="year_to" id="next_year" class="form-control text-dark rounded" disabled> -->
                        <select name="year_to" id="" class="form-control text-dark rounded">
                            <option value="" selected>select next year</option>
                            @foreach(\App\Models\Batch::all() as $batch)
                                <option value="{{$batch->id}}">{{$batch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-50 px-4">
                    <h3 class="py-1 fw-bold text-dark">Class</h3>

                    <div id="section w-100">
                        <div class="form-group py-1 w-100">
                            <label for="cname" class="text-secondary">From </label>
                            <div class="w-100">
                                @php($xyz2 = \App\Models\ClassMaster::where('user_id', auth()->user()->id)->get())
                                <select name="class_from" class="form-control text-dark rounded section" id="section">
                                    @if(count($xyz2) == 1)
                                    <option value="{{$xyz2->first()->class_id}}" selected>{{\App\Http\Controllers\Admin\StudentController::baseClasses()[$xyz2->first()->class_id]}}</option>
                                    @else
                                        <option selected>Not a class master</option>
                                    @endif
                                </select>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div>
                    <div id="section w-100">
                        <div class="form-group py-1 w-100">
                            <label for="cname" class="text-secondary">To </label>
                            <div class="w-100">
                                <select name="class_to" class="form-control text-dark rounded section" id="section">
                                    
                                        @forelse(\App\Http\Controllers\Admin\StudentController::baseClasses() as $key => $section)
                                        <option value="{{$key}}">{{$section}}</option>
                                        @empty
                                        <option>No Sections Created</option>
                                        @endforelse
                                </select>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div>


                    <!-- <div id="section2 w-100">
                        <div class="form-group py-1 w-100">
                            <label for="cname" class="text-secondary">To </label>
                            <div class="w-100" id="section2-input-box">
                                <div class="form-control text-dark rounded" id="section2-name">next class</div>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div> -->
                    


                </div>
            </div>
            <div class="form-group d-flex justify-content-end px-4 mx-4">
                <button type="submit" class="btn btn-primary rounded-lg mx-4 fw-bold">Proceed</button>
            </div>
            <input type="hidden" name="" id="workspace">
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
    // $('.section').on('change', function() {
    //     refresh($(this));
    // })
    // $('.section2').on('change', function() {
    //     refresh2($(this));
    // })

    function refresh(div) {
        $(".pre-loader").css("display", "block");
        url = "{{route('section-children', 'VALUE')}}";
        url = url.replace('VALUE', div.val());
        $.ajax({
            type: "GET",
            url: url,
            success: function(data) {
                $(".pre-loader").css("display", "none");
                let html = "";
                if (data.array.length > 0) {
                    html += '<div class="mt-3"><select onchange="refresh($(this))" class="form-control section" name="class_from">';
                    html += '<option selected disabled > Select ' + data.name + '</option>';
                    for (i = 0; i < data.array.length; i++) {
                        html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
                    }
                    html += '</select>' +
                        '<div class="children"></div></div>';
                }
                div.parent().find('.children').html(html)
            },
            error: function(e) {
                $(".pre-loader").css("display", "none");
            }
        });
    }
    function refresh2(div) {
        $(".pre-loader").css("display", "block");
        url = "{{route('section-children', 'VALUE')}}";
        url = url.replace('VALUE', div.val());
        $.ajax({
            type: "GET",
            url: url,
            success: function(data) {
                $(".pre-loader").css("display", "none");
                let html = "";
                if (data.array.length > 0) {
                    html += '<div class="mt-3"><select onchange="refresh2($(this))" class="form-control section2" name="class_to">';
                    html += '<option selected disabled > Select ' + data.name + '</option>';
                    for (i = 0; i < data.array.length; i++) {
                        html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
                    }
                    html += '</select>' +
                        '<div class="children"></div></div>';
                }
                div.parent().find('.children').html(html)
            },
            error: function(e) {
                $(".pre-loader").css("display", "none");
            }
        });
    }
    function set_target(){
        base_id = $("#section").val();
        if(base_id != null){
            // get target for the given base
            url = "{{route('admin.promotion_target', '_VALUE')}}";
            url = url.replace('_VALUE', base_id)
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'GET',
                success: function(data){
                    // set target data
                    // _data = JSON.parse(data);
                    console.log(data.id);
                    $('#section2-input-box').prepend("<input type='hidden' name='class_to' id='section2' value="+data.id+">");
                    $('#section2-name').text(data.name);
                    refresh2($('#section2'));
                }



            })
        }
    }
</script>
@endsection