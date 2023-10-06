@extends('teacher.layout')
@section('section')
    <div class="col-sm-12">

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr class="text-capitalize">
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_type')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($classes as $class)
                        <tr>
                            <td>{{ \App\Models\SchoolUnits::find($class->department_id)->name }}</td>
                            <td>{{$class->class->type->name}}</td>
                            <td>{{\App\Models\Campus::find($class->campus_id)->name}}</td>
                            <td class="text-capitalize">
                                <a class="btn btn-xs btn-success" href="{{route('user.class_list', [$class->department_id, $class->campus_id])}}?{{request('arg' )=='cr' ? 'arg=cr' : null}}">{{__('text.word_programs')}}</a>
                                <a class="btn btn-xs btn-primary" href="{{route('notifications.index', ['D', $class->department_id, $class->campus_id])}}">{{__('text.word_notifications')}}</a>
                                <a class="btn btn-xs btn-primary" href="{{route('material.index', ['D', $class->department_id, $class->campus_id])}}">{{__('text.word_material')}}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
