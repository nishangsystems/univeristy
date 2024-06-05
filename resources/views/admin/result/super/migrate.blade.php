@extends('admin.layout')
@section('section')
    <div class="py-2 container-fluid">
        <div class="row my-4 container p-3 shadow">
            <div class="col-md-6 col-lg-4 p-2">
                <select class="form-control" name="year" id="form_year" required>
                    <option></option>
                    @foreach (\App\Models\Batch::all() as $year)
                        <option value="{{ $year->id }}" {{ $year->id == old('year', $year_id) ? 'selected' : '' }}>{{ $year->name??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.academic_year') }}</span>
            </div>
            <div class="col-md-6 col-lg-5 p-2">
                <select class="form-control" name="class" id="form_class" required>
                    <option></option>
                    @foreach ($classes??[] as $_class)
                        <option value="{{ $_class['id'] }}" {{ $_class['id'] == old('class', $class_id) ? 'selected' : '' }}>{{ $_class['name']??'' }}</option>
                    @endforeach
                </select>
                <span class="text-secondary">{{ __('text.word_class') }}</span>
            </div>
            <div class="col-lg-3  p-2">
                <button class="btn btn-primary rounded form-control" onclick="submit_form()">{{ __('text.word_results') }}</button>
            </div>
        </div>

        @if($class_id != null)
            <div class="alert-info py-3 text-center border-top border-bottom text-capitalize"><b>{{ $title2 ?? 'Uploading Exam Results' }}</b></div>
            <div class="py-3 text-center border-top border-bottom text-capitalize">{!! $_title2 ?? '----------' !!}</div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="">
                        <div class="alert-warning py-3 text-center text-uppercase border-top border-bottom"><b>@lang('text.upload_results_in_csv_only')</b></div>
                        @if($can_update_exam)
                            <form class="my-4" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input class="form-control my-2 rounded" name="file" type="file">
                                <button class="form-control btn btn-sm btn-primary rounded my-2" type="submit">@lang('text.word_import')</button>
                            </form>
                        @else
                            <div class="alert-danger py-3 border-top border-bottom text-uppercase text-center"><b>@lang('text.cant_import_exam_without_uploading_ca')</b></div>
                        @endif
                        <table>
                            <thead class="text-uppercase">
                                <tr class="bg-light text-dark border-top border-bottom">
                                    {{-- <th>#</th> --}}
                                    <th>A</th>
                                    <th>B</th>
                                    <th>C</th>
                                    <th>D</th>
                                    <th>E</th>
                                </tr>
                                <tr class="bg-light text-danger border-top border-bottom">
                                    {{-- <th></th> --}}
                                    <th>@lang('text.course_code')</th>
                                    <th>@lang('text.word_matricule')</th>
                                    <th>@lang('text.ca_mark')</th>
                                    <th>@lang('text.exam_mark')</th>
                                    <th>@lang('text.word_semester')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-top border-bottom">
                                    <td>CHEM123</td> <td>DNT23D023</td> <td>23</td> <td>54</td> <td class="">1</td> 
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>DNT223</td> <td>DNT23D003</td> <td>20</td> <td>50</td> <td class="">1</td> 
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>DNT233</td> <td>DNT23D102</td> <td>17</td> <td>58</td> <td class="">1</td> 
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>ORG231</td> <td>DNT23D021</td> <td>25</td> <td>45</td> <td class="">1</td> 
                                </tr>
                                <tr class="border-top border-bottom">
                                    <td>SUP123</td> <td>DNT23D025</td> <td>24</td> <td>39</td> <td class="">1</td> 
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="container-fluid shadow rounded py-3">
                        <div class="py-2 my-3 alert alert-info text-center border-top border-bottom">
                            <b>{{ $title2 ?? 'Uploaded Exam Marks' }}</b> <hr class="my-1">
                            <a class="btn btn-danger btn-sm rounded px-5 py-1 text-capitalize text-wrap" onclick="clearResult(`{{ route('admin.result.super.clear', ['year'=>$year_id, 'class'=>$class_id]) }}`)"><b>{{ $delete_label }}</b></a>
                        </div>
                        <table class="table-stripped table">
                            <thead class="text-capitalize border-top border-bottom">
                                <th class="border-left border-right border-light">@lang('text.sn')</th>
                                <th class="border-left border-right border-light">@lang('text.word_matricule')</th>
                                <th class="border-left border-right border-light">@lang('text.course_code')</th>
                                <th class="border-left border-right border-light">@lang('text.academic_year')</th>
                                <th class="border-left border-right border-light">@lang('text.ca_mark')</th>
                                <th class="border-left border-right border-light">@lang('text.exam_mark')</th>
                                <th class="border-left border-right border-light">@lang('text.word_semester')</th>
                            </thead>
                            <tbody>
                                @php
                                    $k = 1;
                                @endphp
                                @foreach ($results??[] as $res)
                                    <tr class="border-top border-bottom">
                                        <td>{{ $k++ }}</td>
                                        <td>{{ $res->student->matric??'' }}</td>
                                        <td>{{ $res->subject->code??'' }}</td>
                                        <td>{{ $res->year->name??'' }}</td>
                                        <td>{{ $res->ca_score??'' }}</td>
                                        <td>{{ $res->exam_score??'' }}</td>
                                        <td>{{ $res->semester_id??'' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>

        let submit_form = function(){
            let year = $('#form_year').val();
            let _class = $('#form_class').val();
            let url = "{{ route('admin.result.super.migrate', ['year'=>'_YR_', 'class'=>'_CLS_']) }}".replace('_YR_', year).replace('_CLS_', _class);
            window.location = url;
        }

        let clearResult = function(route){
            if(confirm(`{{ $delete_prompt??'You are about to clear all exam results for this course' }}`)){
                window.location = route;
            }
        }
        
    </script>
@endsection