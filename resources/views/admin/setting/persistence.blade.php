@extends('admin.layout')
@section('section')
    <div class="py-3">
        <form method="POST" class="container">
            @csrf
            <div class="card card-light my-3">
                <div class="card-body py-0">
                    <div class="row">
                        <div class="col-lg-5 px-3 py-5">
                            <label class="text-secondary text-capitalize">@lang('text.word_operation')</label>
                            <select class="form-control text-uppercase rounded" name="operation" required>
                                <option></option>
                                <option value="fee_settings">PERSIST FEE SETTINGS</option>
                                <option value="course_instructors">PERSIST COURSE INSTRUCTORS</option>
                            </select>
                        </div>
                        <div class="col-lg-7 px-3 py-5 bg-light rounded shadow">
                            <div class="my-2 row mx-1">
                                <label class="col-md-4 text-capitalize">@lang('text.base_year')</label>
                                <select class="col-md-8 form-control rounded" name="base_year" required>
                                    <option></option>
                                    @foreach($years as $key => $year)
                                        <option value="{{ $year->id }}">{{ $year->name??'----' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="my-2 row mx-1">
                                <label class="col-md-4 text-capitalize">@lang('text.target_year')</label>
                                <select class="col-md-8 form-control rounded" name="target_year" required>
                                    <option></option>
                                    @foreach($years as $key => $year)
                                        <option value="{{ $year->id }}">{{ $year->name??'----' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="py-2 d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm rounded form-control text-uppercase" type="submit">@lang('text.word_persist')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection