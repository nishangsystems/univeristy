@extends('admin.layout')
@section('title', 'Available incomes')
@section('section')
<div class="col-sm-12">

    <div class="content-panel">
        <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
            <div>
                <div>
                    <h5 class="font-weight-bold text-capitalize">{{__('text.word_name')}} : <span><label>{{$income->name}}</label></span></h5>
                </div>
                <div>
                    <h5 class="font-weight-bold text-capitalize">{{__('text.word_amount')}} : <span>
                            <label>{{number_format($income->amount)}} {{__('text.currency_cfa')}}</label>
                        </span></h5>
                </div>
                <div>
                    @if($income->status == 1)
                    <h5 class="font-weight-bold text-capitalize">{{__('text.word_status')}} : <span><label>{{__('text.word_active')}}</label></span></h5>
                    @endif
                </div>
                <div>
                    @if($income->status != 1)
                    <h5 class="font-weight-bold text-capitalize">{{__('text.word_status')}} : <span><label>{{__('text.word_inactive')}}</label></span></h5>
                    @endif
                </div>
                <div class="d-inline-flex">
                </div>
            </div>
        </div>

    </div>
    @endsection