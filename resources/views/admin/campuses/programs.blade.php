@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
@endphp
<div class="py-4">
    <table class="table">
        <thead class="text-capitalize">
            <th>###</th>
            <th>{{__('text.word_programs')}}</th>
            <th>{{__('text.word_fee')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach(\App\Models\ProgramLevel::join('school_units', 'school_units.id', '=', 'program_levels.program_id')->get(['program_levels.*']) as $program_level)
            <tr>
                <td>{{$k++}}</td>
                <td>{{ \App\Models\SchoolUnits::find($program_level->program_id)->name.' : Level '. \App\Models\Level::find($program_level->level_id)->level }}</td>
                <td>{{ \App\Models\CampusProgram::where('campus_id', request('id'))->where('program_level_id', $program_level->id)->count() > 0 ?
                    \App\Models\CampusProgram::where('campus_id', request('id'))->where('program_level_id', $program_level->id)->first()->payment_items()->where('year_id', $year)->where('name', 'TUTION')->first()->amount ?? '----':
                     '----'}}</td>
                <td>
                    @if(in_array($program_level->id, $programs))
                    <a href="{{route('admin.campuses.set_fee', [request('id'), $program_level->id])}}" class="btn btn-sm btn-primary">{{__('text.word_fees')}}</a>
                    <!-- if class already has courses assigned to it, it can be dropped again -->
                        @if ($program_level->class_subjects()->count() == 0)
                        <a href="{{route('admin.campuses.drop_program', [request('id'), $program_level->id])}}" class="btn btn-sm btn-danger">{{__('text.word_drop')}}</a>
                        @endif
                    @else
                    <a href="{{route('admin.campuses.add_program', [request('id'), $program_level->id])}}" class="btn btn-sm btn-success">{{__('text.word_add')}}</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection