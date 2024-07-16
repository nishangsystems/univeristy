@extends('admin.layout')
@section('section')
    <div class="py-3">
        <form method="POST" class="container">
            @csrf
            <div class="card card-light my-3">
                <div class="card-body py-0">
                    <div class="row">
                        <div class="col-lg-5 px-3 py-5">
                            <label class="text-secondary text-capitalize">@lang('text.word_operations')</label>
                            <div class="text-uppercase container-fluid text-secondary">
                                <span class="mx-2 my-1 py-1 border-bottom d-flex"><input value="fee_settings" type="checkbox" class="mx-2" name="operation[]">PERSIST FEE SETTINGS</span>
                                <span class="mx-2 my-1 py-1 border-bottom d-flex"><input value="course_instructors" type="checkbox" class="mx-2" name="operation[]">PERSIST COURSE INSTRUCTORS</span>
                                <span class="mx-2 my-1 py-1 border-bottom d-flex"><input value="class_delegates" type="checkbox" class="mx-2" name="operation[]">CLASS DELEGATES</span>
                                <span class="mx-2 my-1 py-1 border-bottom d-flex"><input value="hods" type="checkbox" class="mx-2" name="operation[]">HODs</span>
                            </div>
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