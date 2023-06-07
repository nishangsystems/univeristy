@extends('documentation.layout')
@section('section')
    <div class="py-5 my-3 card px-5 bg-light">
        <form class="my-3" method="post">
            @csrf
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_title')}}</label>
                <input class="form-control" name="title" required>
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_parent')}}</label>
                <select class="form-control" name="parent_id" required>
                    <option value="0" class="text-capitalize">{{__('text.word_documentation')}}</option>
                    @foreach (\App\Models\Documentation::all() as $doc)
                        <option value="{{$doc->id}}" {{request('parent')==$doc->id ? 'selected' : ''}}>{{$doc->fullname()}}</option>
                    @endforeach
                </select>
            </div>
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_role')}}</label>
                <select class="form-control text-capitalize" name="role" required>
                    <option value="teacher" class="text-capitalize" {{isset($parent) && $parent->role == 'teacher' ? 'selected' : ''}}>{{trans_choice('text.word_teacher', 1)}}</option>
                    <option value="student" class="text-capitalize" {{isset($parent) && $parent->role == 'student' ? 'selected' : ''}}>{{trans_choice('text.word_student', 1)}}</option>
                    <option value="admin" class="text-capitalize" {{isset($parent) && $parent->role == 'admin' ? 'selected' : ''}}>{{__('text.word_admin')}}</option>
                    <!-- @foreach (\App\Models\Role::all() as $role)
                        <option value="{{$role->slug}}" {{isset($parent) && $parent->role == $role->slug ? 'selected' : ''}}>{{$role->name}}</option>
                    @endforeach -->
                </select>
            </div>
            @if (isset($parent) && $parent->role == 'admin')
                <div class="py-2">
                    <label class="text-capitalize">{{__('text.word_permissions')}}</label>
                    <select class="form-control text-capitalize" name="permission" required>
                        @foreach (\App\Models\Permission::all() as $role)
                            <option value="{{$role->slug}}" {{isset($parent) && $parent->permission == $role->slug ? 'selected' : ''}}>{{$role->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="py-2">
                <label class="text-capitalize">{{__('text.word_content')}}</label>
                <textarea class="form-control" name="content" id="doc_content" rows="4"></textarea>
            </div>
            <div class="py-2 d-flex justify-content-end">
                <input class="btn btn-xs btn-primary" type="submit" value="{{__('text.word_save')}}">
            </div>
        </form>
    </div>
@endsection

@section('script')
    <!-- <script src="{{ asset('public/assets/js') }}/ckeditor/ckeditor.js"></script> -->
    <script>
        var editor1 = new RichTextEditor("#doc_content");
        // CKEDITOR.replace('doc_content');
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