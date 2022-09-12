@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.fee.list.store',$unit->id)}}">
                <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.enter_fee_details')}}</h5>
                @csrf
                <div class="form-group @error('name') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_name')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="name" value="{{old('name')}}" type="text" required/>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group @error('amount') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_amount')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required/>
                        @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>
                        <a class="btn btn-xs btn-danger text-capitalize" href="{{route('admin.fee.list.index', $unit->id)}}" type="button">{{__('text.word_cancel')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
