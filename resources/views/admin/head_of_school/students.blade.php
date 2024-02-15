@extends('admin.layout')
@section('section')
    <div class="my-3">
        <form>
            <div class="row my-5 container-fluid rounded-md">
                <div class="col-md-4 col-lg-3 p-2"> 
                    <div class="text-secondary">@lang('text.word_level')</div>
                    <select class="form-control" name="level_id">
                        <option></option>
                        @foreach (\App\Models\Level::all() as $level)
                            <option value="{{ $level->id }}" {{  request('level_id') == $level->id ? 'selected' : '' }}>LEVEL {{ $level->level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-3 p-2"> 
                    <div class="text-secondary">@lang('text.word_year')</div>
                    <select class="form-control" name="year_id">
                        <option></option>
                        @foreach (\App\Models\Batch::all() as $year)
                            <option value="{{ $year->id }}" {{  request('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name??'' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-3 p-2"> 
                    <div class="text-secondary">@lang('text.word_status')</div>
                    <select class="form-control" name="active">
                        <option {{  request('active') == null ? 'selected' : '' }}></option>
                        <option value="1" {{  request('active') == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{  request('active') == 0 ? 'selected' : '' }}>Non-Active</option>
                    </select>
                </div>
                <div class="col-md-4 col-lg-3 p-2"> 
                    <button class="btn btn-secondary btn-sm rounded" type="submit">@lang('text.word_students')</div>
                </div>
            </div>
        </form>
        
        <hr class="my-3">

        <table class="table adv-table">
            <thead class="text-capitalize fw-semibold">
                <th>#</th>
                <th>@lang('text.word_name')</th>
                <th>@lang('text.word_matricule')</th>
                @if (!request()->has('class_id'))
                    <th>@lang('text.word_class')</th>
                @endif
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $student->name??'--student-name--' }}</td>
                        <td>{{ $student->matric }}</td>
                        @if (!request()->has('class_id'))
                            <td>{{ $student->_class(request('year'))->name() }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection