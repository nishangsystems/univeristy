@extends('student.layout')
@php
    $c_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $header = \App\Helpers\Helpers::instance()->getHeader();
@endphp
@section('section')
<div class="mx-3">

    <div class="form-panel" id="TUTION-PANEL">
        @if($student->total_balance() > 0)
        <form class="form-horizontal" role="form" method="POST">
            <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.enter_fee_details')}}</h5>
            @csrf
            <input type="hidden" name="student_id" value="{{auth('student')->id()}}">
            <input type="hidden" name="year_id" value="{{$c_year}}">
            <input type="hidden" name="payment_purpose" value="TUTION">
            <div class="form-group row">
                <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.total_fee')}}: </label>
                <div class="col-sm-10">
                    <input for="cname" class="form-control" value="{{number_format($total_fee)}} CFA" disabled></input>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.scholarship_award')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" value="{{number_format($scholarship)}} CFA" disabled></input>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.balance_fee')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" name="balance" value="{{number_format($student->total_balance())}} CFA" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.extra_fee')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" name="xtra-fee" value="{{$student->extraFee($c_year) == null ? 0 : $student->extraFee($c_year)->amount}} CFA" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_debt')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" name="xtra-fee" value="{{$student->total_debts($c_year)-$student->bal($student->id, $c_year)}} CFA" disabled>
                </div>
            </div>
            <div class="form-group @error('item') has-error @enderror">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.word_item')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="payment_id">
                        <option value="" disabled class="text-capitalize">{{__('text.select_item')}}</option>
                        @foreach($student->class(\App\Helpers\Helpers::instance()->getYear())->payment_items()->where(['year_id'=>$c_year])->get() ?? [] as $item)
                        <option value="{{$item->id}}">{{$item->name." - ".$item->amount}} FCFA</option>
                        @endforeach
                    </select>
                    @error('item')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('amount') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_amount')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required />
                    @error('amount')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.payment_number')}}<span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="tel" value="{{old('tel')}}" type="number" required />
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.make_payment')}}</button>
                </div>
            </div>
        </form>
        @else
        <div class="alert alert-success text-center text-capitalize">{{__('text.phrase_fee_complete')}} <span class="mx-3 fw-bolder">{{$student->debt($c_year) == 0 ? '' : 'DEBT : '.$student->debt($c_year)}}</span></div>
        @endif

        
    </div>


    
</div>
@endsection