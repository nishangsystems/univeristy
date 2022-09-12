<?php

use Illuminate\Support\Facades\Http;

?>
@extends('admin.layout')
@section('section')
<div class="w-100 row">
    <div class="w-100 py-3 col-sm-8 col-lg-6 mx-auto">
        <h2 class="fw-bolder text-dark mx-auto text-center">Student Demotion</h2>
        <div class="w-100 py-1">
            <form action="{{route('admin.students.init_demotion')}}" method="get w-100">
                <div class="form-group w-100">
                    <div class=" text-secondary fs-3 my-2 text-center">
                        You may achieve demotion by selecting a promotion batch or performing a custom operation.
                    </div>
                    <div class="form-group w-100">
                        <label for="promotion_method" class=" text-dark">Demote by: </label>
                        <div class="mx-0 d-flex flex-wrap py-1 justify-content-between">
                            <select name="promotion_method" required class=" form-control text-dark rounded col-sm-9">
                                <option value="" selected>select method</option>
                                <option value="promotion">promotion batch</option>
                                <option value="custom">custom demotion</option>
                            </select>
                            <input type="submit" value="continue" class="btn btn-default btn-sm ">
                            
                        </div>
                    </div>
                </div>
            </form>
            <form action="{{route('admin.students.init_demotion')}}" method="get">
                <div class="settings_section w-100">
                    @if(request('promotion_method') == 'promotion')
                       <div class="form-group">
                            <label for="" class="text-secondary fs-3 fw-bold">select promotion batch:</label>
                            <div class="d-flex flex-wrap py-1 justify-content-between">
                                <select required name="promotion_batch" id="promotion_batch" class="text-dark fw-bold form-control rounded col-sm-9">
                                    <option value="">select batch</option>
                                    @foreach(\App\Models\Promotion::all() as $promotion)
                                        <option value="{{$promotion->id}}">{{$promotion->created_at}} set</option>
                                    @endforeach
                                </select>
                                <input type="submit" value="confirm" class="btn btn-sm btn-default">
                            </div>
                       </div>
                    @endif
                </div>
            </form>
			@if(request('promotion_batch') != '')
				<form action="{{route('admin.students.demotion')}}", method="get" class=" w-100">
                    <div class="form-group promotion-details">
                        <div class="form-group py-2">
                            <label for="" class="text-secondary fs-3 fw-bold form-control col-sm-9">{{\App\Models\Promotion::find(request('promotion_batch'))->created_at}}</label>
                            <input type="hidden" name="promotion_batch" value="{{request('promotion_batch')}}">
                        </div>
                        @php $this_promotion = \App\Models\Promotion::find(request('promotion_batch')) @endphp
                        <div class="d-flex flex-wrap justify-content-between w-100">
                            <div class="col-sm-9 row mx-0 px-0">
                                <div class="col-sm-6 mx-0 pr-1">
                                    <h2 class="text-center fw-bolder text-dark">From</h2>
                                    <div class="py-1 mx-0">
                                        <input type="hidden" value="{{$this_promotion->to_year}}" name="year_from">
                                        <div class="border-bottom text-dark">{{\App\Models\Batch::find($this_promotion->to_year)->name}}</div>
                                    </div>
                                    <div class="py-1 mx-0">
                                        <input type="hidden" value="{{$this_promotion->to_class}}" name="class_from">
                                        <div class="border-bottom text-dark">{{$classes[$this_promotion->to_class]}}</div>
                                    </div>
                                </div>
                                <div class=" col-sm-6 mx-0 pl-1">
                                    <h2 class="text-center fw-bolder text-dark">To</h2>
                                    <div class="py-1 mx-0">
                                        <input type="hidden" value="{{$this_promotion->from_year}}" name="year_to">
                                        <div class="border-bottom text-dark">{{\App\Models\Batch::find($this_promotion->from_year)->name}}</div>
                                    </div>
                                    <div class="py-1 mx-0">
                                        <input type="hidden" value="{{$this_promotion->from_class}}" name="class_to">
                                        <div class="border-bottom text-dark">{{$classes[$this_promotion->from_class]}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-auto d-flex flex-col justify-content-end">
                                <input type="submit" value="Confirm" class="btn btn-default btn-sm">
                            </div>
                        </div>
                    </div>
				</form>
			@endif
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    // $('#promotion_batch').on('change', showPromotion($(this)));

    // function showPromotion(inp){
    //     let url = "{{route('admin.promotion_batch', '_id_')}}";
    //     url = url.replace('_id_', inp.val());
    //     $.ajax({
    //         method: 'GET',
    //         url: url,
    //         success: function(data){
    //             // fill in returned data into a form and display
    //             htmlText = `
                	// <div class="row w-100">
                    //     <div class col-sm-6 col-md-5 mx-auto>
                    //         <h2 class="text-center fw-bolder text-dark">From</h2>
                    //         <div class="py-1">
                    //             <input type="hidden" value="${data.from_year_id}" name="from_year_id">
                    //             <div class="form-control text-dark">${data.from_year}</div>
                    //         </div>
                    //         <div class="py-1">
                    //             <input type="hidden" value="${data.from_class_id}" name="from_class_id">
                    //             <div class="form-control text-dark">${data.from_class}</div>
                    //         </div>
                    //     </div>
                    //     <div class col-sm-6 col-md-5 mx-auto>
                    //         <h2 class="text-center fw-bolder text-dark">To</h2>
                    //         <div class="py-1">
                    //             <input type="hidden" value="${data.to_year_id}" name="to_year_id">
                    //             <div class="form-control text-dark">${data.to_year}</div>
                    //         </div>
                    //         <div class="py-1">
                    //             <input type="hidden" value="${data.to_class_id}" name="to_year_id">
                    //             <div class="form-control text-dark">${data.to_class}</div>
                    //         </div>
                    //     </div>
                    //     <div class="d-flex justify-content-end"><input type="submit" value="Confirm" class="btn btn-primary"></div>
                    // </div>
    //                 `;
    //             $('.promotion-details').html(htmlText);
    //         },
    //         error: function(er){
    //             console.log(er);
    //         }
    //     });
    // }

 

</script>
@endsection