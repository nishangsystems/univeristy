@extends('teacher.layout')
@section('section')
@php($k = 1)
    <div class="py-4">
        @if(request('id') == null)
            <table class="table">
                <thead class="text-capitalize">
                    <th>#</th>
                    <th>{{__('text.word_class')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach($classes as $pl)
                        @if($pl['department'] != request('department_id'))
                            @continue
                        @endif
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $pl['name'] }}</td>
                            <td class="text-capitalize">
                                @if((\Auth::user()->campus_id != null) && in_array($pl['id'], \App\Models\Campus::find(\Auth::user()->campus_id)->campus_programs()->pluck('program_level_id')->toArray()))
                                    <a href="{{Request::url().'?id='.$pl['id']}}" class="btn btn-sm btn-primary">{{__('text.word_students')}}</a>
                                @endif
                                @if(request('arg') == 'cr')
                                    <a href="{{route('user.programs.course_report', ['program_level_id'=>$pl['id']])}}" class="btn btn-sm btn-info">{{__('text.course_report')}}</a>
                                @else
                                    <a href="{{route('notifications.index', ['C', $pl['id'], request('campus_id') ?? 0])}}" class="btn btn-sm btn-success">{{__('text.word_notifications')}}</a>
                                    <a href="{{route('material.index', ['C', $pl['id'], request('campus_id') ?? 0])}}" class="btn btn-sm btn-primary">{{__('text.program_material')}}</a>
                                    <a href="{{route('user.programs.courses', $pl['id'])}}" class="btn btn-sm btn-success">{{__('text.word_subjects')}}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="table adv-table">
                <thead class="text-capitalize">
                    <th>#</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_matricule')}}</th>
                </thead>
                <tbody>
                    @foreach($students as $stud)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$stud->name}}</td>
                            <td>{{$stud->matric}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection