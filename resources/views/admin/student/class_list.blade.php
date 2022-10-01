@extends('admin.layout')
@section('section')
<div class="py-4">
    <table class="table">
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
            @if(!request()->has('id'))
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_class')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\ProgramLevel::orderBy('program_id', 'ASC')->get() as $pl)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $pl->program()->first()->name .' : Level '.$pl->level()->first()->level }}</td>
                            <td>
                                <a href="{{Request::url().'?id='.$pl->id}}" class="btn btn-sm btn-primary">{{__('text.word_students')}}</a>
                                <a href="{{Request::url().'?action=campuses&id='.$pl->id}}" class="btn btn-sm btn-success">{{__('text.word_campuses')}}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
            @if(request()->has('id') && request('action')!='campuses')
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_matricule')}}</th>
                    <th>{{__('text.academic_year')}}</th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\Students::where('program_id', request('id'))->where('admission_batch_id', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->get() as $stud)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$stud->name}}</td>
                            <td>{{$stud->matric}}</td>
                            <td>{{\App\Models\Batch::find($stud->admission_batch_id)->name ?? '----'}}</td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
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