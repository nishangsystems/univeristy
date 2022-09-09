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
                        <div class="item-title text-capitalize">
                            <h3>{{request('role')?\App\Models\Role::whereSlug(request('role'))->first()->byLocale()->name." Permissions":__('text.user_permissions')}}</h3>
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
                            @foreach($permissions as $role)
                                <tr>
                                    <td>{{$role->byLocale()->name}}</td>
                                    <td align="right">
                                        <a class="btn btn-primary text-capitalize" href="{{route('admin.users.index')}}?permission={{$role->slug}}">{{__('text.word_users')}}</a>
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
