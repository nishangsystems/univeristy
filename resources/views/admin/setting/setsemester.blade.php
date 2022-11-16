@extends('admin.layout')
@section('section')
<div class="py-3">
    <form method="get">
        <div class="input-group-merge border border-light rounded d-flex my-4">
            <label for="" class="input-group-text text-capitalize border-0 fs">{{__('text.word_background')}}</label>
            <select name="background" id="" class="form-control border-0">
                <option value=""></option>
                @foreach($backgrounds as $bg)
                    <option value="{{$bg->id}}">{{$bg->background_name}}</option>
                @endforeach
            </select>
            <input type="submit" value="{{__('text.word_get')}}" class="border-0">
        </div>
    </form>
    <table class="table">
        <thead class="text-capitalize">
            <th>{{__('text.sn')}}</th>
            <th>{{__('text.word_semester')}}</th>
            <th></th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($semesters ?? [] as $sem)
                <tr>
                    <td>{{$k++}}</td>
                    <td>{{$sem->name}}</td>
                    <td>
                        <a onclick="event.preventDefault(); document.querySelector('#form-{{$sem->id}}').submit()" class="btn {{$sem->status == 1 ? 'btn-info' :'btn-primary'}} btn-md">{{$sem->status == 1 ? __('text.word_semester') :__('text.set_semester')}}</a>
                        <form action="{{route('admin.postsemester', [$sem->id])}}" method="post" id="form-{{$sem->id}}" class="hidden">@csrf
                            <input type="hidden" name="background" id="" value="{{request('background')}}">
                        </form>
                    </td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection