@extends('parents.layout')
@php
    $c_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $header = \App\Helpers\Helpers::instance()->getHeader();
@endphp
@section('section')
<div class="mx-3">

    <div class="form-panel" id="TUTION-PANEL">
        <form class="form-horizontal" role="form" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $parent->id }}">
            <input type="hidden" name="year_id" value="{{$c_year}}">
            <input type="hidden" name="payment_purpose" value="PLATFORM">
            <input type="hidden" name="payment_id" value="{{ $year->id }}">
            <div class="form-group row">
                <label for="cname" class="control-label col-lg-2 text-capitalize"></label>
                <div class="col-sm-10 py-4 text-info text-center" style="font-size: x-large;">
                    {{ __('text.platform_payments_template_text', ['amount'=>$amount, 'purpose'=>__('text.platform_charges'), 'semester'=>null, 'year'=>$year->name??'']) }}
                </div>
            </div>
            <div class="form-group @error('amount') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_amount')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="amount" value="{{ $amount }}" type="number" required readonly/>
                    @error('amount')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.payment_number')}}<span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="tel" value="{{$parent->phone??null}}" type="number" required />
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.make_payment')}}</button>
                </div>
            </div>
        </form>
    </div>


    
</div>
@endsection