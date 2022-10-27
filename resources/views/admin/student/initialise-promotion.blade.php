<?php

use Illuminate\Support\Facades\Http;

?>
@extends('admin.layout')
@section('section')
<div class="container-fluid h-screen d-flex flex-column justify-content-center">
    <div class="w-75 mx-auto d-block py-3 px-5 rounded-lg">
        <form action="{{route('admin.students.promotion')}}" method="get" class="w-100 p-2 ">
            @csrf
            <h2 class="my-3 text-dark fw-bolder text-center w-100 text-capitalize">{{__('text.student_promotion')}}</h2>
            <div class="w-100 py-2 d-md-flex text-capitalize">
                <div class="w-50 px-4"> 
                    <h3 class="py-1 fw-bold text-dark ">{{__('text.academic_year')}}</h3>
                    <div class="form-group w-100 py-1">
                        <label for="year_from" class="text-secondary">{{__('text.word_from')}}:</label>
                        <select name="year_from" id="" class="form-control text-dark rounded" onchange="set_next_year()" required>
                            <option value="" selected>{{__('text.select_year')}}</option>
                            @foreach(\App\Models\Batch::all() as $batch)
                                <option value="{{$batch->id}}">{{$batch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group w-100 py-1">
                        <label for="year_to" class="text-secondary">{{__('text.word_to')}}:</label>
                        <!-- <input type="text" name="year_to" id="next_year" class="form-control text-dark rounded" disabled> -->
                        <select name="year_to" id="" class="form-control text-dark rounded">
                            <option value="" selected>{{__('text.select_target_year')}}</option>
                            @foreach(\App\Models\Batch::all() as $batch)
                                <option value="{{$batch->id}}">{{$batch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-50 px-4 text-capitalize">
                    <h3 class="py-1 fw-bold text-dark">{{__('text.word_class')}}</h3>

                    <div id="section w-100">
                        <div class="form-group py-1 w-100">
                            <label for="cname" class="text-secondary">{{__('text.word_from')}} </label>
                            <div class="w-100">
                                <select name="class_from" class="form-control text-dark rounded section text-capitalize" id="section" onchange="set_target()">
                                    <option selected disabled>{{__('text.select_section')}}</option>
                                    @forelse(\App\Http\Controllers\Admin\StudentController::baseClasses() as $key=>$section)
                                    <option value="{{$key}}">{{$section}}</option>
                                    @empty
                                    <option>{{__('text.no_sections_created')}}</option>
                                    @endforelse
                                </select>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div>
                    <div id="section w-100">
                        <div class="form-group py-1 w-100 text-capitalize">
                            <label for="cname" class="text-secondary">{{__('text.word_to')}} </label>
                            <div class="w-100">
                                <select name="class_to" class="form-control text-dark rounded section" id="section" onchange="set_target()">
                                    <option selected disabled>{{__('text.select_target_section')}}</option>
                                    @forelse(\App\Http\Controllers\Admin\StudentController::baseClasses() as $key=>$section)
                                    <option value="{{$key}}">{{$section}}</option>
                                    @empty
                                    <option>{{__('text.no_sections_created')}}</option>
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
                <button type="submit" class="btn btn-primary rounded-lg mx-4 fw-bold">{{__('text.word_proceed')}}</button>
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

    // function refresh(div) {
    //     $(".pre-loader").css("display", "block");
    //     url = "{{route('section-children', 'VALUE')}}";
    //     url = url.replace('VALUE', div.val());
    //     $.ajax({
    //         type: "GET",
    //         url: url,
    //         success: function(data) {
    //             $(".pre-loader").css("display", "none");
    //             let html = "";
    //             if (data.array.length > 0) {
    //                 html += '<div class="mt-3"><select onchange="refresh($(this))" class="form-control section" name="class_from">';
    //                 html += '<option selected disabled > Select ' + data.name + '</option>';
    //                 for (i = 0; i < data.array.length; i++) {
    //                     html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
    //                 }
    //                 html += '</select>' +
    //                     '<div class="children"></div></div>';
    //             }
    //             div.parent().find('.children').html(html)
    //         },
    //         error: function(e) {
    //             $(".pre-loader").css("display", "none");
    //         }
    //     });
    // }
    // function refresh2(div) {
    //     $(".pre-loader").css("display", "block");
    //     url = "{{route('section-children', 'VALUE')}}";
    //     url = url.replace('VALUE', div.val());
    //     $.ajax({
    //         type: "GET",
    //         url: url,
    //         success: function(data) {
    //             $(".pre-loader").css("display", "none");
    //             let html = "";
    //             if (data.array.length > 0) {
    //                 html += '<div class="mt-3"><select onchange="refresh2($(this))" class="form-control section2" name="class_to">';
    //                 html += '<option selected disabled > Select ' + data.name + '</option>';
    //                 for (i = 0; i < data.array.length; i++) {
    //                     html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
    //                 }
    //                 html += '</select>' +
    //                     '<div class="children"></div></div>';
    //             }
    //             div.parent().find('.children').html(html)
    //         },
    //         error: function(e) {
    //             $(".pre-loader").css("display", "none");
    //         }
    //     });
    // }
    // function set_target(){
    //     base_id = $("#section").val();
    //     if(base_id != null){
    //         // get target for the given base
    //         url = "{{route('admin.promotion_target', '_VALUE')}}";
    //         url = url.replace('_VALUE', base_id)
    //         $.ajax({
    //             url: url,
    //             dataType: 'json',
    //             type: 'GET',
    //             success: function(data){
    //                 // set target data
    //                 // _data = JSON.parse(data);
    //                 console.log(data.id);
    //                 $('#section2-input-box').prepend("<input type='hidden' name='class_to' id='section2' value="+data.id+">");
    //                 $('#section2-name').text(data.name);
    //                 refresh2($('#section2'));
    //             }



    //         })
    //     }
    // }
</script>
@endsection