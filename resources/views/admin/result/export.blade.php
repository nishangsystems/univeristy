@extends('admin.layout')
@section('section')
<div class="mx-3">
    <h5 class="text-muted font-weight-bold text-capitalize">
        {{__('text.export_student_results')}}
    </h5>
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST">
            @csrf
            <div class="form-group @error('yeah') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_batch')}}</label>
                <div class="col-lg-10">
                    <select class="form-control" name="year">
                        <option selected disabled class="text-capitalize"> {{__('text.select_year')}} </option>
                        @foreach(\App\Models\Batch::all() as $year)
                        <option {{ old('year') == $year->id ? 'selected':''}} value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('sequence') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_sequence')}}</label>
                <div class="col-lg-10">
                    <select class="form-control" name="sequence">
                        <option selected disabled>{{__('text.select_sequence')}}</option>
                        @foreach(\App\Models\Sequence::all() as $sequence)
                        <option {{ old('sequence') == $sequence->id ? 'selected':''}} value="{{$sequence->id}}">{{$sequence->name}}</option>
                        @endforeach
                    </select>
                    @error('sequence')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_export')}}</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection