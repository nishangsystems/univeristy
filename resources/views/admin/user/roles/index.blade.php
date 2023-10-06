@extends('admin.layout')
@section('title')
    Admin Dashboard
@endsection


@section('section')

    <div>
        <div class="col-8-xxxl col-12">
            <div class="card height-auto">
                <div class="card-body">
                    <div class="heading-layout1">
                        <div class="item-title d-flex align-items-center justify-content-between text-capitalize">
                            <h3>{{__('text.user_roles')}}</h3>
                            <a href="{{route('admin.roles.create')}}" class="btn btn-success">{{__('text.add_role')}}</a>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table class="table display data-table text-nowrap">
                            <thead>
                            <tr>
                                <th>{{__('text.word_name')}}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{$role->byLocale()->name}}</td>
                                    <td align="right">
                                        @if(\Auth::user()->campus_id == null)<a class="btn btn-success" href="{{route('admin.roles.edit',$role->slug)}}?role={{$role->slug}}"> {{__('text.word_edit')}}</a>@endif
                                        <a class="btn btn-primary" href="{{route('admin.users.index')}}?role={{$role->slug}}">{{__('text.word_users')}}</a>
                                        <a class="btn btn-info" href="{{route('admin.roles.permissions')}}?role={{$role->slug}}">{{__('text.word_permissions')}}</a>
                                        <a class="btn btn-danger" onclick="confirm('You are about to delete role: {{$role->name}}. Any users associated to this role will be deleted.') ? $('#_delete_form_{{$role->id}}').submit() : null">{{__('text.word_delete')}}</a>
                                        <form method="post" action="{{route('admin.roles.destroy', $role->id)}}" id="_delete_form_{{$role->id}}">@csrf</form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- All Transport List Area End Here -->
    </div>
    <!-- All Subjects Area End Here -->
@endsection
