@extends('student.layout')
@php
    $c_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $header = \App\Helpers\Helpers::instance()->getHeader();
@endphp
@section('section')
<div class="mx-3">

    

    <!-- FOR PAYMENT OF OTHER ITEMS -->
    <div class="form-panel" id="OTHERS-PANEL">
        <form class="form-horizontal" role="form" method="POST">
            <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.enter_payment_details')}}</h5>
            @csrf
            <input type="hidden" name="payment_purpose" value="OTHERS">
            <input type="hidden" name="student_id" value="{{auth('student')->id()}}">
            <input type="hidden" name="year_id" value="{{$c_year}}">
            <input type="hidden" name="amount" value="" id="amount_field">
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_amount')}}<span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <label class=" form-control bg-light" id="amount"></label>
                </div>
            </div>
            <div class="form-group @error('item') has-error @enderror">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.word_item')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="payment_id" id="payment_id">
                        <option value="" class="text-capitalize">{{__('text.select_item')}}</option>
                        @foreach(\App\Models\Income::where(['cash'=>0, 'pay_online'=>true])->get() ?? [] as $item)
                        <option value="{{$item->id}}">{{$item->name." - ".$item->amount}} FCFA</option>
                        @endforeach
                    </select>
                    @error('item')
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
                    <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_proceed')}}</button>
                </div>
            </div>
        </form>

    </div>
    
</div>
@endsection
@section('script')
    <script>
        $('#payment_id').on('change', function(){
            value = $('#payment_id').val();
            if(value != '' && value != null){
                url = "{{route('get-income-item', '__ID__')}}";
                url = url.replace('__ID__', value);
                $.ajax({
                    method: 'get',
                    url: url,
                    success: function(data){
                        console.log(data);
                        $('#amount').html(data.amount+' CFA');
                        $('#amount_field').val(data.amount);
                    }
                })
            }
        });

    </script>
@endsection