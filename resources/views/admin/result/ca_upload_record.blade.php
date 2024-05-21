@extends('admin.layout')
@section('section')
   <div class="py-2 container-fluid">
        <div class="row my-4 container p-3 shadow">
            <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                <select class="form-control" name="year" id="form_year" required>
                    <option></option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" {{ $year->id == old('year', $year_id) ? 'selected' : '' }}>{{ $year->name??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.academic_year') }}</span>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                <select class="form-control" name="semester" id="form_semester" required>
                    <option></option>
                    @foreach ($semesters??[] as $semester)
                        <option value="{{ $semester->id }}" {{ $semester->id == old('semester', $semester_id) ? 'selected' : '' }}>{{ $semester->name??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.word_semester') }}</span>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3 p-2">
                <select class="form-control" name="class" id="form_class" required>
                    <option></option>
                    @foreach ($classes??[] as $class)
                        <option value="{{ $class['id'] }}" {{ $class['id'] == old('class', $class_id) ? 'selected' : '' }}>{{ $class['name']??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.word_semester') }}</span>
            </div>
            <div class="col-sm-6 col-md-12 col-lg-3 p-2">
                <button class="btn btn-primary rounded form-control" onclick="submit_form()">{{ __('text.word_results') }}</button>
            </div>
        </div>
        @isset($record)
            <div class="my-4 py-2">
                <table class="table">
                    <thead class="text-capitalize border-top border-bottom">
                        <th>@lang('text.sn')</th>
                        <th>@lang('text.course_code')</th>
                        <th>@lang('text.course_title')</th>
                        <th>@lang('text.word_status')</th>
                    </thead>
                    <tbody>
                        @php
                            $k = 1;
                        @endphp
                        @foreach ($record as $row)
                            <tr class="border-bottom">
                                <td>{{ $k++ }}</td>
                                <td>{{ $row->code??'' }}</td>
                                <td>{{ $row->name??'' }}</td>
                                <td>
                                    @if ($row->_status == 1)
                                        <span class="fa fa-check text-success"></span>
                                    @else
                                        <span class="fa fa-times text-danger"></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endisset
    </div>
@endsection

@section('script')
    <script>
        let submit_form = function(){
            let _yr = $('#form_year');
            let _sm = $('#form_semester');
            let _cl = $('#form_class');
            let url = "{{ route('admin.result.ca.upload_report', ['program_level'=>'__CLID__', 'semester'=>'__STR__', 'year'=>'__YR__']) }}".replace('__CLID__', _cl).replace('__STR__', _sm).replace('__YR__', _yr);
            window.location = url
        }
    </script>
@endsection