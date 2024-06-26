@extends('admin.layout')
@section('section')
    <div class="py-2">
        <div class="container-fluid">
            <table class="table-light">
                <thead class="text-capitalize">
                    <tr class="border-bottom">
                        <th colspan="5" class="text-center header">@lang('text.set_course_instructor')</th>
                    </tr>
                    <tr>
                        <th>@lang('text.sn')</th>
                        <th>@lang('text.word_name')</th>
                        <th>@lang('text.word_email')</th>
                        <th>@lang('text.word_phone')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach($users as $key => $user)
                        <tr class="border-bottom" style="background-color: {{$user->is_master == 1 ? '#defedf' : ''}}">
                            <td>{{$k++}}</td>
                            <td>{{$user->user->name??''}}</td>
                            <td>{{$user->user->email??''}}</td>
                            <td>{{$user->user->phone??''}}</td>
                            <td>
                                <form method="POST">
                                    @csrf
                                    <input type="hidden" name="instructor" value="{{$user->id}}">
                                    <button class="btn btn-sm rounded btn-primary" type="submit">@lang('text.word_assign')</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection