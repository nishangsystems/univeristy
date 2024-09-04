@extends('admin.layout')
@section('section')
    <div class="py-4 container-fluid">
        <div class="row py-5">
            <div class="col-md-5 col-lg-5">
                <div class="d-flex justify-content-end">
                    @if ($has_exam == true)
                        <a href="{{route('admin.result.decoded.students.undo', ['course_id'=>request('course_id'), 'semester_id'=>request('semester_id'), 'year_id'=>request('year_id')])}}" class="btn btn-sm rounded text-capitalize btn-primary">@lang('text.clear_decoding')</a>
                    @else
                        <div class="alert alert-danger border-top border-bottom">Nothing to clear. No deocdede exams marks where found.</div>
                    @endif
                </div>
                <hr>
                <div class="py-3 my-4 text-center">
                    <table class="table">
                        <thead class="text-uppercase">
                            <tr>
                                <th>#</th>
                                <th>@lang('text.word_class')</th>
                                <th>@lang('text.word_count')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $k = 1; @endphp
                            @foreach ($decoding_summary as $item)
                                <tr>
                                    <td>{{$k++;}}</td>
                                    <td>{{$item->_class->name()}}</td>
                                    <td>{{$item->size}}</td>
                                    <td>
                                        <a href="{{route('admin.result.decoded.course.class.students', ['class_id'=>$item->class_id, 'course_id'=>$course->id, 'semester_id'=>$semester->id, 'year_id'=>$year->id])}}" class="btn btn-xs rounded text-capitalize btn-primary">@lang('text.word_students')</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-7 col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead class="text-capitalize">
                                <tr>
                                    <th colspan="5" class="text-center">@isset($class){{$class->name()}}@else @lang('text.word_students') @endisset</th>
                                </tr>
                                <th>#</th>
                                <th>@lang('text.word_name')</th>
                                <th>@lang('text.word_matricule')</th>
                                <th>@lang('text.exam_mark')</th>
                                {{-- <th>@lang('text.word_action')</th> --}}
                            </thead>
                            <tbody>
                                @php
                                    $k =1;
                                @endphp
                                @isset($student_codes)
                                    @foreach ($student_codes as $code)
                                        <tr>
                                            <td>{{$k++}}</td>
                                            <td>{{$code->name}}</td>
                                            <td>{{$code->matric}}</td>
                                            <td>{{$code->exam_score}}</td>
                                            {{-- <td>
                                                @if($code->exam_score == null)
                                                    <a href="{{route('admin.result.encoded.course.student.uncode', ['course_id'=>request('course_id'), 'semester_id'=>request('semester_id'), 'year_id'=>request('year_id'), 'result_id'=>$code->id])}}" class="btn btn-xs btn-danger rounded text-capitalize">@lang('text.word_delete')</a>
                                                @endif
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                @endisset
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@endsection
