@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">

        <div class="row py-5">
            <div class="col-md-8 col-xl-9">
                <form method="post" class="card " enctype="multipart/form-data">
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
            </div>
            <div class="d-flex justify-content-end py-3 px-2 col-md-4 col-xl-3">
                <table class="table-info col-md-10 col-lg-8">
                    <thead class="text-capitalize">
                        <tr>
                            <th colspan="2" class="text-center border-bottom border-white">@lang('text.data_format')</th>
                        </tr>
                        <tr class="border-bottom border-dark">
                            <th>@lang('text.course_code')</th>
                            <th>@lang('text.exam_code')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < 3; $i++)
                            <tr>
                                <td>---------</td>
                                <td>---------</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>


@endsection
