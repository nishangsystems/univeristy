@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="my-5 py-5 container-fluid">
            <div class="text-center fs-3 fw-semibold text-secondary my-3">Download Student Data</div>
            <form method="POST">
                @csrf
                <div class="py-2 row">
                    <div class="text-secondary col-sm-3 text-capitalize">@lang('text.academic_year')</div>
                    <div class="col-sm-9">
                        <select class="form-control" name="year_id" required>
                            <option></option>
                            @foreach ($years as $year)
                                <option value="{{ $year->id }}" {{ old('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="py-2 row">
                    <div class="text-secondary col-sm-3 text-capitalize">@lang('text.word_class')</div>
                    <div class="col-sm-9">
                        <select class="form-control" name="class_id" required>
                            <option></option>
                            @foreach ($classes as $class)
                                <option value="{{ $class['id'] }}" {{ old('class_id') == $class['id'] ? 'selected' : '' }}>{{ $class['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="py-2 d-flex justify-content-end">
                    <button class="btn btn-primary btn-sm" type="submit">@lang('text.word_download')</button>
                </div>
            </form>
        </div>
    </div>
@endsection