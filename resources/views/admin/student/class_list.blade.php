@extends('admin.layout')
@section('section')
<div class="py-4">
    <table class="table">
        @if(!request()->has('id'))
            <thead>
                <th>###</th>
                <th>Class</th>
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
        <thead>
            <th>###</th>
            
        </thead>
        @endif
    </table>
</div>
@endsection