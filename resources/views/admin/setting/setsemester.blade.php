@extends('admin.layout')
@section('section')
<div class="py-3">
    <table class="table">
        <thead class="text-capitalize">
            <th>{{__('text.sn')}}</th>
            <th>{{__('text.word_background')}}</th>
            <th>{{__('text.word_semester')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($semesters ?? [] as $sem)
                <tr>
                    <td class="border-left border-right border-light">{{$k++}}</td>
                    <td class="border-left border-right border-light">{{$sem->background_name}}</td>
                    <td class="border-left border-right border-light">{{$sem->name}}</td>
                    <td class="border-left border-right border-light">
                        <a onclick="event.preventDefault(); document.querySelector('#form-{{$sem->id}}').submit()" class="btn {{$sem->status == 1 ? 'btn-warning' :'btn-primary'}} btn-md">{{$sem->status == 1 ? __('text.current_semester') :__('text.set_semester')}}</a>
                        <form action="{{route('admin.postsemester', [$sem->id])}}" method="post" id="form-{{$sem->id}}" class="hidden">@csrf
                            <input type="hidden" name="background" id="" value="{{$sem->background_id}}">
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection