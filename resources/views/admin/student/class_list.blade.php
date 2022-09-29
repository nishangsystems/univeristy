@extends('admin.layout')
@section('section')
<div class="py-4">
    <table class="table">
        @if(!request()->has('id'))
            <thead class="text-capitalize">
                <th>###</th>
                <th>{{__('text.word_class')}}</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\ProgramLevel::all() as $pl)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ \App\Models\SchoolUnits::find($pl->program_id)->name .' : Level '.\App\Models\Level::find($pl->level_id)->level }}</td>
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
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\Students::where('program_id', request('id'))->get() as $stud)
                    <tr>
                        <td>{{$k++}}</td>
                        <td>{{$stud->name}}</td>
                        <td>{{$stud->matric}}</td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        @if(request()->has('id') && request('action') =='campuses')
        @endif
    </table>
</div>
@endsection