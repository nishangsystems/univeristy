@extends('teacher.layout')
@section('section')
    <div class="col-sm-12">
        <p class="text-muted">
           My Classes
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($classes as $class)
                        <tr>
                            <td>{{ \App\Models\SchoolUnits::find($class->department_id)->name }}</td>
                            <td>{{$class->class->type->name}}</td>
                            <td class="text-capitalize">
                                <a class="btn btn-xs btn-primary" href="{{route('user.class_list')}}">{{__('text.word_students')}}</a>
                                <a class="btn btn-xs btn-success" href="{{route('user.course_list')}}">{{__('text.word_courses')}}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
