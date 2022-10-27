@extends('admin.layout')
@section('section')
<div class="mx-3">
    <h5 class="text-muted font-weight-bold text-capitalize">
        {{__('text.import_student_results')}}
    </h5>

    <div class="form-panel">
        <form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST">
            @csrf
            <div class="form-group @error('file') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Excel file ({{__('required')}})</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="file" value="{{old('file')}}" type="file" required />
                    @error('file')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection