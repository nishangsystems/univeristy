@extends('teacher.layout')
@section('section')
@php
    $department_id = \App\Models\ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')[0];
    $program_ids = \App\Http\Controllers\HomeController::x_children($department_id)->pluck('id');
    $program_level_ids = \App\Models\ProgramLevel::whereIn('program_id', $program_ids)->pluck('id');
@endphp
<div class="py-4">
    <table class="table">
        <!-- list program level students per campus -->
        @if(request()->has('campus_id'))
            <thead class="text-capitalize">
                <th>###</th>
                <th>{{__('text.word_name')}}</th>
                <th>{{__('text.word_matricule')}}</th>
                <th>{{__('text.academic_year')}}</th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\Students::where('program_id', request('id'))->where('campus_id', request('campus_id'))->where('admission_batch_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->get() as $stud)
                    <tr>
                        <td>{{$k++}}</td>
                        <td>{{$stud->name}}</td>
                        <td>{{$stud->matric}}</td>
                        <td>{{\App\Models\Batch::find($stud->admission_batch_id)->name ?? '----'}}</td>
                    </tr>
                @endforeach
            </tbody>
        @else
        <!-- list program levels (classes) -->
            @if(!request()->has('id'))
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_class')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
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
            @endif
            <!-- general class list for a program level -->
            @if(request()->has('id') && !request()->has('action'))
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_matricule')}}</th>
                    <th>{{__('text.academic_year')}}</th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\Students::where('program_id', request('id'))->where('admission_batch_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->get() as $stud)
                        @if((\Auth::user()->campus_id != null) && ($stud->campus_id == \Auth::user()->campus_id))
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$stud->name}}</td>
                            <td>{{$stud->matric}}</td>
                            <td>{{\App\Models\Batch::find($stud->admission_batch_id)->name ?? '----'}}</td>
                        </tr>
                        @endif
                        @if(\Auth::user()->campus_id == null)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$stud->name}}</td>
                            <td>{{$stud->matric}}</td>
                            <td>{{\App\Models\Batch::find($stud->admission_batch_id)->name ?? '----'}}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            @endif

            <!-- List campuses for a given program level -->
            @if(request()->has('id') && request('action') =='campuses')
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_campus')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\ProgramLevel::find(request('id'))->campuses()->get() as $campus)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$campus->name}}</td>
                            <td>
                                <a href="{{Request::url().'?action=campus_students&id='.request('id').'&campus_id='.$campus->id}}" class="btn btn-success btn-sm">{{__('text.word_students')}}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        @endif
    </table>
</div>
@endsection