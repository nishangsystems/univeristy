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
            <th>{{__('text.word_tution')}}</th>
            <th>{{__('text.international_tution')}}</th>
            <th>{{__('text.first_instalment')}}</th>
            <th>{{__('text.second_instalment')}}</th>
            <th>{{__('text.word_registration')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($classes as $program_level)
            <tr>
                <td>{{$k++}}</td>
                <td>{{ $program_level->name() }}</td>
                <td>{{ $program_level->amount ?? '----'}}</td>
                <td>{{ $program_level->international_amount ?? '----'}}</td>
                <td>{{ $program_level->first_instalment ?? '----'}}</td>
                <td>{{ $program_level->second_instalment ?? '----'}}</td>
                <td>{{ $program_level->reg ?? '----'}}</td>
                <td>
                    <a href="{{route('admin.campuses.set_fee', [$campus->id, $program_level->id])}}" class="btn btn-xs btn-primary rounded mb-1"><span class="fa fa-cog mr-1"></span>{{__('text.word_fees')}}</a>
                    @if($program_level->tution_id != null)
                        <a href="{{route('admin.import_fee', ['fee_id'=>$program_level->tution_id])}}" class="btn btn-xs btn-success rounded mb-1"><span class="fa fa-upload mr-1"></span>{{__('text.import_tution')}}</a>
                    @endif
                    @if($program_level->reg_id != null)
                        <a href="{{route('admin.import_fee', ['fee_id'=>$program_level->reg_id])}}" class="btn btn-xs btn-info rounded mb-1"><span class="fa fa-upload mr-1"></span>{{__('text.import_registration')}}</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection