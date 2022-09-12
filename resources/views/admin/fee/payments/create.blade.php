@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.fee.student.payments.store',$student->id)}}">
            <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.enter_fee_details')}}</h5>
            @csrf
            <div class="form-group row">
                <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.totoal_fee')}}: </label>
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
                    <input for="cname" class="form-control" name="balance" value="{{number_format($balance)}} CFA" disabled></input>
                </div>
            </div>
            <div class="form-group @error('item') has-error @enderror">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.word_item')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="item">
                        <option value="" disabled class="text-capitalize">{{__('text.select_item')}}</option>
                        @foreach($student->class(\App\Helpers\Helpers::instance()->getYear())->items as $item)
                        <option selected value="{{$item->id}}">{{$item->name." - ".$item->amount}} FCFA</option>
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
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_date')}}<span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="date" value="{{old('amount')}}" type="date" required />
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger " href="{{route('admin.fee.student.payments.index', $student->id)}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection