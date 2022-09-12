@extends('admin.layout')
@section('title', 'Create Scholarship Award')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.scholarship.update', $scholarship->id)}}">
            @csrf
            @method('PUT')
            <div class="form-group @error('name') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_name')}} <span style="color: red;">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="name" value="{{old('name') ?? $scholarship->name}}" type="text" required />
                    @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('amount') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_amount')}} <span style="color: red;">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="amount" value="{{old('amount') ?? $scholarship->amount}}" type="number" required />
                    @error('amount')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('status') has-error @enderror">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.scholarship_status')}} <span style="color: red;">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="status">
                        <option value="1" {{$scholarship->status == 1? 'selected':''}}>{{__('text.word_active')}}</option>
                        <option value="0" {{$scholarship->status == 0? 'selected':''}}>{{__('text.word_inactive')}}</option>
                    </select>
                    @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger" href="#" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection