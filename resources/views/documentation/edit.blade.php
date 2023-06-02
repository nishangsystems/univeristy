@extends('documentation.layout')
@section('section')
    <div class="d-flex justify-content-end py-4"><a class="btn btn-xs btn-primary rounded" href="{{route('documentation.create', [request('id')])}}">{{__('text.add_child')}}</a></div>
    <div class="py-5 my-3 card col-md-9 mx-auto px-5 bg-light">
        <form class="my-3" method="post">
            @csrf
            <div class="py-2">
                <label>{{__('text.word_title')}}</label>
                <input class="form-control" name="title" required value="{{$item->title}}">
            </div>
            <div class="py-2">
                <label>{{__('text.word_parent')}}</label>
                <select class="form-control" name="parent_id" required>
                    <option value="0" class="text-capitalize">{{__('text.word_documentation')}}</option>
                    @foreach (\App\Models\Documentation::all() as $doc)
                        <option value="{{$doc->id}}" {{$item->parent_id==$doc->id ? 'selected' : ''}}>{{$doc->fullname()}}</option>
                    @endforeach
                </select>
            </div>
            <div class="py-2">
                <label>{{__('text.word_role')}}</label>
                <select class="form-control" name="role" required>
                    <option value="teacher" class="text-capitalize" {{$item->role == 'teacher' ? 'selected' : ''}}>{{trans_choice('text.word_teacher', 1)}}</option>
                    <option value="student" class="text-capitalize" {{$item->role == 'student' ? 'selected' : ''}}>{{trans_choice('text.word_student', 1)}}</option>
                    @foreach (\App\Models\Role::all() as $role)
                        <option value="{{$role->slug}}" {{$item->role == $role->slug ? 'selected' : ''}}>{{$role->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="py-2">
                <label>{{__('text.word_content')}}</label>
                <textarea class="form-control" name="content" id="doc_content" rows="4">{{$item->content}}</textarea>
            </div>
            <div class="py-2 d-flex justify-content-end">
                <input class="btn btn-xs btn-primary" type="submit" value="{{__('text.word_save')}}">
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script src="{{ asset('public/assets/js') }}/ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('doc_content');
    </script>
    <script>
        function __loadContent(params) {
            console.log(params);
            $('#attendance_id_field').val(params);
            // let attendance = $(params).attr(data);
            // console.log(attendance);
            $('#content').removeClass('hidden')
        }
    </script>
@endsection