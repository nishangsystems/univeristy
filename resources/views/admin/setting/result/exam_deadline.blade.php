@extends('admin.layout')
@section('section')
<div class="py-3">

    <form method="post" class="card mx-5 my-5">
        @csrf
        <div class="card-body">
            <div class="row my-2">
                <span class="col-md-3 text-capitalize">@lang('text.word_semester')</span>
                <div class="col-sm-9">
                    <select class="form-control" name="semester_id" required>
                        <option></option>
                        @foreach ($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}> {{ $semester->name??'' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row my-2">
                <span class="col-md-3 text-capitalize">@lang('text.word_date')</span>
                <div class="col-sm-9">
                    <input class="form-control" name="exam_upload_latest_date" type="date" required value="{{ old('exam_upload_latest_date') }}">
                </div>
            </div>
            <div class="d-flex justify-content-end my-3">
                <button class="btn btn-sm btn-primary text-capitalize" type="submit">@lang('text.word_save')</button>
            </div>
        </div>
    </form>
    

    <table class="table">
        <thead class="text-capitalize">
            <th></th>
            <th>@lang('text.word_semester')</th>
            <th>@lang('text.word_date')</th>
        </thead>
        @php
            $k = 1;
        @endphp
        @foreach($semesters as $key => $semester)
            <tr>
                <td>{{ $k++ }}</td>
                <td>{{ $semester->name??'' }}</td>
                <td>{{ $semester->exam_upload_latest_date == null ? null :$semester->exam_upload_latest_date->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </table>
</div>
@endsection