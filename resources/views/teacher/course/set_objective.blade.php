@extends('teacher.layout')
@section('section')
    <div class="col-md-6 mx-auto my-4">
        <form class="form py-4 px-3" method="post">
            @csrf
            <label class="text-capitalize my-2">{{__('text.word_objective')}}</label>
            <textarea class="form-control rounded w-100" rows="4" name="objective" id="objective" required>{!! $subject->objective !!}</textarea>
            <div class="d-flex justify-content-end py-2">
                <input type="submit" value="{{__('text.word_save')}}" class="btn btn-sm btn-primary">
            </div>
        </form>
        <div class="mt-4 card">
            <h4 class="card-header mb-2 text-primary text-capitalize text-center border-b border-light">
                {{__('text.course_objective')}}
            </h4>
            <div class="text-justify card-body">{!! $subject->objective !!}</div>
        </div>
    </div>
    <div class="col-md-6 mx-auto my-4">
        <form class="form py-4 px-3" method="post">
            @csrf
            <label class="text-capitalize my-2">{{__('text.expected_outcomes')}}</label>
            <textarea class="form-control rounded w-100" rows="4" name="outcomes" id="outcomes" required>{!! $subject->outcomes !!}</textarea>
            <div class="d-flex justify-content-end py-2">
                <input type="submit" value="{{__('text.word_save')}}" class="btn btn-sm btn-primary">
            </div>
        </form>
        <div class="mt-4 card">
            <h4 class="card-header mb-2 text-primary text-capitalize text-center border-b border-light">
                {{__('text.expected_outcomes')}}
            </h4>
            <div class="text-justify card-body">{!! $subject->outcomes !!}</div>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script>
        var editor1 = new RichTextEditor("#objective");
        var editor2 = new RichTextEditor("#outcomes");
    </script> --}}

    <script src="{{ asset('assets/js') }}/ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('objective');
        CKEDITOR.replace('outcomes');
    </script>
@endsection