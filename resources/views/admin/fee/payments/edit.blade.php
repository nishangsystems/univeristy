@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.fee.student.payments.update',[$student->id, $payment->id])}}">
                <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.enter_fee_details')}}</h5>
                @csrf
                <input type="hidden" name="_method" value="put">
                <div class="form-group @error('item') has-error @enderror">
                    <label  class="control-label col-lg-2 text-capitalize">{{__('text.word_item')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="item">
                            <option value="" disabled>{{__('text.select_item')}}</option>
                            <option value="0">{{__('text.other_items')}}</option>
                            @foreach($student->class(\App\Helpers\Helpers::instance()->getYear())->items as $item)
                                <option {{old('item', $payment->payment_id) == $item->id?'selected':''}} value="{{$item->id}}">{{$item->name." - ".$item->amount}} {{__(text.currency_cfa)}}</option>
                            @endforeach
                        </select>
                        @error('item')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group @error('amount') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_amount')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="amount" value="{{old('amount',$payment->amount)}}" type="number" required/>
                        @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.fee.student.payments.index', $student->id)}}" type="button">{{__('text.word_cancel')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
