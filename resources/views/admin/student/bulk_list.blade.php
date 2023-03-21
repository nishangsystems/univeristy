@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') == null ? \App\Helpers\Helpers::instance()->getCurrentAccademicYear() : request('year_id');
    $request = request();
@endphp
<div class="py-4">
    <div class="col-lg-12">
        <div class="input-group-merge d-flex rounded border border-dark my-3">
            <select class="form-control col-sm-10" name="year" id="year_filter_field">
                <option></option>
                @foreach (\App\Models\Batch::all() as $batch)
                    <option value="{{$batch->id}}" {{$batch->id == $year ? 'selected' : ''}}>{{$batch->name}}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-dark text-capitalize col-sm-2 text-center" onclick="event.preventDefault(); window.location = `{{route('admin.student.bulk.list', ['filter'=>$request->filter, 'item_id'=>$request->item_id, 'year_id'=>'__YID__'])}}`.replace('__YID__', $('#year_filter_field').val())">{{__('text.word_get')}}</button>
        </div>
    </div>
    <div class="w-100 fw-bolder d-flex justify-content-center py-2 h4">
        <span class="text-dark text-uppercase">{{__('text.word_total')}} : {{count($students)}}</span>
    </div>
    <table class="table">
        <thead class="text-capitalize">
            <th>###</th>
            <th>{{__('text.word_name')}}</th>
            <th>{{__('text.word_matricule')}}</th>
            @if (request('filer')=='program')
                <th>{{__('text.word_level')}}</th>
                @else
                <th>{{__('text.word_class')}}</th>
            @endif
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($students as $stud)
                <tr>
                    <td>{{$k++}}</td>
                    <td>{{$stud->name}}</td>
                    <td>{{$stud->matric}}</td>
                    <td>{{$stud->_class($year)->name()}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection