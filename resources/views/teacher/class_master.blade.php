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
                            <td>{{ $options[$class->class->id] }}</td>
                            <td>{{$class->class->type->name}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{route('user.class.student', [$class->class->id])}}">Students</a>
                                <a class="btn btn-xs btn-success" href="{{route('user.subject')}}?class={{$class->class->id}}">Subjects</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
