@extends('admin.layout')
@section('section')
    <!-- Breadcubs Area End Here -->
    <!-- Add Expense Area Start Here -->
    <div class="card height-auto">
        <div class="card-body">
            <div class="heading-layout1">
                <div class="item-title text-capitalize">
                    <h3>{{__('text.add_new_role')}}</h3>
                </div>
            </div>
            <form class="new-added-form" method="post" action="{{route('admin.roles.assign.post')}}">
                @csrf
                <div class="row">
                    <div class="col-12 form-group">
                        <label class="text-capitalize">{{__('text.select_user')}}</label>
                        <select name="user_id" class="select2 form-control" required>
                           @if($user != null)
                                <option {{($user->slug == request('user'))?'selected':''}} value="{{$user->id}}"> {{$user->name}}</option>
                            @else
                                <option value="">{{__('text.please_select_user')}}</option>
                                @foreach(\App\Models\User::where('type','admin')->get() as $user)
                                    @if(!$user->hasRole('admin'))
                                        <option {{($user->slug == request('user'))?'selected':''}} value="{{$user->id}}"> {{$user->name}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12 form-group">
                        <label class="text-capitalize">{{__('text.select_user')}}</label>
                        <select name="role_id" class="select2 form-control" required>
                            <option>{{__('text.please_select_user')}}</option>
                            @foreach(\App\Models\Role::get() as $role)
                               @if($role->slug != 'admin')
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                               @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn-fill-lg btn-gradient-yellow btn-hover-bluedark">{{__('text.word_save')}}</button>
                        <a href="{{route('admin.roles.index')}}" class="btn-fill-lg bg-blue-dark btn-hover-yellow">{{__('text.word_reset')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection



