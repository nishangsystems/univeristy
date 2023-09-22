@extends('admin.layout')
@section('section')
<div class="py-3">
    <table class="table">
        <thead class="text-capitalize">
            <th>{{__('text.sn')}}</th>
            <th>{{__('text.word_background')}}</th>
            <th>{{__('text.result_access_min_fee')}}</th>
            <th>{{__('text.word_semester')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($semesters ?? [] as $sem)
                <tr>
                    <td class="border-left border-right border-light">{{$k++}}</td>
                    <td class="border-left border-right border-light">{{$sem->background_name}}</td>
                    <td class="border-left border-right border-light">
                        <form action="{{route('admin.postsemester.minfee', [$sem->id])}}" method="post">
                            @csrf
                            <div class="input-group">
                                <select name="semester_min_fee" class="form-control">
                                    <option></option>
                                    <option value="0.25" {{ $sem->semester_min_fee == 0.25 ? 'selected' : '' }}>25%</option>
                                    <option value="0.5" {{ $sem->semester_min_fee == 0.5 ? 'selected' : '' }}>50%</option>
                                    <option value="0.75" {{ $sem->semester_min_fee == 0.75 ? 'selected' : '' }}>75%</option>
                                    <option value="0.9" {{ $sem->semester_min_fee == 0.9 ? 'selected' : '' }}>90%</option>
                                    <option value="0.95" {{ $sem->semester_min_fee == 0.95 ? 'selected' : '' }}>95%</option>
                                    <option value="1" {{ $sem->semester_min_fee == 1 ? 'selected' : '' }}>100%</option>
                                </select>
                                <button class="input-group-icon rounded btn-sm" type="submit">{{ __('text.word_update') }}</button>
                            </div>
                        </form>
                    </td>
                    <td class="border-left border-right border-light">{{$sem->name}}</td>
                    <td class="border-left border-right border-light">
                        <form action="{{route('admin.postsemester', [$sem->id])}}" method="post" id="form-{{$sem->id}}">@csrf
                            <button type="submit" class="btn {{$sem->status == 1 ? 'btn-warning' :'btn-primary'}} btn-md">{{$sem->status == 1 ? __('text.current_semester') :__('text.set_semester')}}</button>
                            <input type="hidden" name="background" id="" value="{{$sem->background_id}}">
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection