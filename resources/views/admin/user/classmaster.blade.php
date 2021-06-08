@extends('admin.layout')

@section('section')
    <div class="col-sm-12">
        <p class="text-muted">
            <a href="{{route('admin.users.classmaster.create')}}?type={{request('type')}}" class="btn btn-info btn-xs">Add Class Master</a>
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Batch</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $k=>$user)
                            <tr>
                                <td>{{$k+1}}</td>
                                <td>{{$user->user->name}}</td>
                                <td>{{$user->class->name}}</td>
                                <td>{{$user->batch->name}}</td>
                                <td  class="d-flex justify-content-end align-items-center" >
                                    <a onclick="event.preventDefault();
                                            document.getElementById('delete{{$user->id}}').submit();" class=" btn btn-danger btn-xs m-2">Un Assign</a>
                                    <form id="delete{{$user->id}}" action="{{route('admin.users.classmaster')}}" method="POST" style="display: none;">
                                        @method('DELETE')
                                        <input type="hidden" name="master" value="{{$user->id}}">
                                        {{ csrf_field() }}
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{$users->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection
