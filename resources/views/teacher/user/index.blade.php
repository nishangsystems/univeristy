@extends('teacher.layout')

@section('section')
    <div class="col-sm-12">
        <p class="text-muted">
            <a href="{{route('user.teacher.create')}}?type={{$type}}" class="btn btn-info btn-xs">Add {{request('type')}}</a>
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_email')}}</th>
                        <th>{{__('text.word_phone')}}</th>
                        <th>{{__('text.word_address')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th>{{__('text.word_gender')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $k=>$user)
                            <tr>
                                <td>{{$k+1}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->phone}}</td>
                                <td>{{$user->address}}</td>
                                <td>{{$user->campus_id ? \App\Models\Campus::find($user->campus_id)->name ?? '' : ''}}</td>
                                <td>{{$user->gender}}</td>
                                <td  class="d-flex justify-content-end align-items-center" >
                                    <a class="btn btn-xs btn-primary" href="{{route('user.teacher.show',[$user->id])}}"><i class="fa fa-eye"> Profile</i></a> |
                                    {{-- <a class="btn btn-xs btn-success" href="{{route('user.teacher.edit',[$user->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                                    <a onclick="event.preventDefault();
                                            document.getElementById('delete{{$user->id}}').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                                    <form id="delete{{$user->id}}" action="{{route('user.teacher.destroy',$user->id)}}" method="POST" style="display: none;">
                                        @method('DELETE')
                                        {{ csrf_field() }}
                                    </form> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                </div>
            </div>
        </div>
    </div>
@endsection
