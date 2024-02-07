@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.pay_income.store', [$class_id, $student_id])}}">

            @csrf

            <div class="form-group @error('income_id') has-error @enderror mt-5">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.income_type')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="income_id" onchange="updateAmont(this)">
                        <option value="">{{__('text.select_income_type')}} </option>
                        @foreach($incomes as $key => $income)
                         <option value="{{$income->id}}" data-amount="{{ $income->amount??0 }}">{{$income->name }}, {{$income->type}}</option>
                        @endforeach
                    </select>
                    @error('income_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('batch_id') has-error @enderror mt-4">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.word_year')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="batch_id">
                        <option value="">{{__('text.select_year')}}</option>
                        @foreach($years as $key => $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('batch_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('amount') has-error @enderror mt-4">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.word_amount')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input type="number" class="form-control" name="amount" id="amount_field" value="{{ old('amount') }}">
                    @error('amount')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
    <script>
        let updateAmont = function(selectElement){
            option = selectElement.options[selectElement.selectedIndex];
            amount = $(option).attr('data-amount');
            // alert(amount);
            $('#amount_field').val(amount);
        }
    </script>
@endsection