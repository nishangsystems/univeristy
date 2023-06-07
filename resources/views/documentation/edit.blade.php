@extends('documentation.layout')
@section('section')
    <div class="d-flex justify-content-end py-4"><a class="btn btn-xs btn-primary rounded" href="{{route('documentation.create', [request('id')])}}">{{__('text.add_child')}}</a></div>
    <div class="py-5 my-3 card px-5 bg-light">
        <form class="my-3" method="post">
            @csrf
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_title')}}</label>
                <input class="form-control" name="title" required value="{{$item->title}}">
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_parent')}}</label>
                <select class="form-control" name="parent_id" required>
                    <option value="0" class="text-capitalize">{{__('text.word_documentation')}}</option>
                    @foreach (\App\Models\Documentation::all() as $doc)
                        <option value="{{$doc->id}}" {{$item->parent_id==$doc->id ? 'selected' : ''}}>{{$doc->fullname()}}</option>
                    @endforeach
                </select>
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_role')}}</label>
                <select class="form-control" name="role" required>
                    <option value="teacher" class="text-capitalize" {{$item->role == 'teacher' ? 'selected' : ''}}>{{trans_choice('text.word_teacher', 1)}}</option>
                    <option value="student" class="text-capitalize" {{$item->role == 'student' ? 'selected' : ''}}>{{trans_choice('text.word_student', 1)}}</option>
                    <option value="admin" class="text-capitalize" {{$item->role == 'admin' ? 'selected' : ''}}>{{trans_choice('text.word_admin', 1)}}</option>

                </select>
            </div>
            @if ( $item->role == 'teacher')
                <div class="py-2">
                    <label class="text-capitalize">{{__('text.word_permission')}}</label>
                    <select class="form-control text-capitalize" name="permission" required>
                        <option value="normal_lecturer" {{isset($parent) && $parent->permission == 'normal_lecturer' ? 'selected' : ''}}>{{__('text.normal_lecturer')}}</option>
                        <option value="hod" {{isset($parent) && $parent->permission == 'hod' ? 'selected' : ''}}>{{__('text.word_HOD')}}</option>
                    </select>
                </div>
            @elseif ($item->role == 'admin')
                <div class="py-2">
                    <label class="text-capitalize">{{__('text.word_permission')}}</label>
                    <select class="form-control text-capitalize" name="permission" required>
                        @foreach (\App\Models\Permission::orderBy('name', 'ASC')->get() as $perm)
                            <option value="{{$perm->slug}}" {{$item->permission == $perm->slug ? 'selected' : ''}}>{{$perm->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_content')}}</label>
                <textarea class="form-control" name="content" id="doc_content" rows="4">{{$item->content}}</textarea>
            </div>
            <div class="py-2 d-flex justify-content-end">
                <input class="btn btn-xs btn-primary" type="submit" value="{{__('text.word_save')}}">
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        var editor1 = new RichTextEditor("#doc_content");
    </script>
    <script>
        function __loadContent(params) {
            console.log(params);
            $('#attendance_id_field').val(params);
            $('#content').removeClass('hidden')
        }
    </script>
@endsection