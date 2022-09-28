@extends('admin.layout')
@section('section')
<div class="py-4">
    <table class="table">
        <thead class="text-capitalize">
            <th>S/N</th>
            <th>{{__('text.word_level')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach(\App\Models\Level::All() as $level)
            <tr style="background-color: {{in_array($level->id, $program_levels) ? '#f4fcfc' : '#fff'}}">
                <td>{{$k++}}</td>
                <td>{{$level->level}}</td>
                <td>
                    <a href="{{route('admin.units.subjects', [request('id'), $level->id])}}" class="btn btn-sm btn-primary">{{__('text.word_subjects')}}</a>|
                    @if(!in_array($level->id, $program_levels))
                    <a href="{{route('admin.programs.levels.add', [request('id'), $level->id])}}" class="btn btn-sm btn-success">{{__('text.word_add')}}</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection