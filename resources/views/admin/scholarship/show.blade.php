@extends('admin.layout')
@section('title', 'Available Scholarships')
@section('section')
<div class="col-sm-12">
    <div class="col-sm-12">
        <!-- <div class="mb-3 d-flex justify-content-start">
            <h3 class="font-weight-bold">Available Scholarships</h3>
        </div> -->
        <!-- <div class="text-muted mb-3 d-flex justify-content-end">
            <a href="{{route('admin.scholarship.create')}}" class="btn btn-info btn-xs">Add Scholarship</a>
        </div> -->
    </div>

    <div class="content-panel">

        <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
            <div>
                <div>
                    <h5 class="font-weight-bold">{{__('text.word_name')}}: <span><label>{{$data->name}}</label></span></h5>
                </div>
                <div>
                    <h5 class="font-weight-bold">{{__('text.word_amount')}} : <span>
                            <label>{{number_format($data->amount)}} FCFA</label>
                        </span></h5>
                </div>
                <div>
                    <h5 class="font-weight-bold">{{__('text.scholarship_type')}}: <span><label>{{$data->type}}</label></span></h5>
                </div>
                <div>
                    @if($data->status == 1)
                    <h5 class="font-weight-bold">{{__('text.word_status')}}: <span><label>{{__('text.word_active')}}</label></span></h5>
                    @endif
                </div>
                <div>
                    @if($data->status == 0)
                    <h5 class="font-weight-bold">{{__('text.word_status')}}: <span><label> {{__('text.word_inactive')}}</label></span></h5>
                    @endif
                </div>

            </div>
            <div class="d-inline-flex">
            </div>
        </div>

    </div>


</div>
@endsection