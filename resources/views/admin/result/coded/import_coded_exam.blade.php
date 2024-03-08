@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <form method="post" class="card">
            @csrf
            {{-- <div class="text-secondary card-header">Update result code for {{ $course->name??'' }} [{{ $course->code??'' }}]</div> --}}
            <div class="row card-bopdy">
                <div class="text-white text-capitalize col-sm-12 col-md-3">
                    <span class="form-control border-0 bg-dark">@lang('text.word_file') [.csv]</span>
                </div>
                <div class="col-col-sm-9 col-md-7">
                    <input type="file" name="file" id="" class="form-control rounded border-0 border-bottom" placeholder="select file" value="{{ old('file') }}">
                </div>
                <div class="col-col-sm-3 col-md-2">
                    <button class="form-control btn btn-secondary" type="submit">@lang('text.word_import')</button>
                </div>
            </div>
        </form>

@endsection
