@extends('admin.layout')

@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.users.update', $user->id)}}">

                <input name="_method" value="put" type="hidden" />
                @csrf
                <div class="form-group @error('matric') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_matricule')}}</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="matric" value="{{old('matric', $user->matric)}}" type="text" required />
                        @error('matric')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('name') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">Full Name (required)</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="name" value="{{old('name', $user->name)}}" type="text" required />
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="form-group @error('email') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">Username (required)</label>
                    <div class="col-lg-10">
                        <input class=" form-control" readonly name="email" value="{{old('email', $user->email)}}" type="text" required />
                        @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('phone') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">Phone</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="phone" value="{{old('phone', $user->phone)}}" type="text" required />
                        @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('address') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">Address</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="address" value="{{old('address', $user->address)}}" type="text" required />
                        @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('gender') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">Gender</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="gender">
                            <option selected disabled>Select Gender</option>
                            <option {{old('gender', $user->gender) == 'male'?'selected':''}} value="male">Male</option>
                            <option {{old('gender', $user->gender) == 'female'?'selected':''}} value="female">Female</option>
                        </select>
                        @error('gender')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('type') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">Type</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="type">
                            <option selected disabled>{{__('text.select_type')}}</option>
                            <option {{old('type', $user->type) == 'teacher'?'selected':''}} value="teacher">Teacher</option>
                            <option {{old('type', $user->type) == 'admin'?'selected':''}} value="admin">Admin</option>
                        </select>
                        @error('type')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('campus_id') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_campus')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="campus_id">
                            <option>{{__('text.select_campus')}}</option>
                            @foreach (\App\Models\Campus::all() as $campus)
                                <option value="{{$campus->id}}" {{$campus->id == \App\Models\User::find($user->id)->campus_id ? 'selected' : ''}}>{{$campus->name}}</option>
                            @endforeach
                        </select>
                        @error('type')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                @if ($user->type == 'admin')
                    <div class="form-group @error('role_id') has-error @enderror">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_role')}}</label>
                        <div class="col-lg-10">
                            <select class="form-control" name="role_id">
                                <option>{{__('text.word_role')}}</option>
                                @foreach (\App\Models\Role::all() as $role)
                                    <option value="{{$role->id}}" {{$role->id == \App\Models\User::find($user->id)->roleR()->first()->role_id??null ? 'selected' : ''}}>{{$role->name}}</option>
                                @endforeach
                            </select>
                            @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-xs btn-primary" type="submit">Save</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.users.index')}}" type="button">Cancel</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
