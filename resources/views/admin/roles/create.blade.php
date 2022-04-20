@extends('admin.layout')
@section('section')
    <!-- Breadcubs Area End Here -->
    <!-- Add Expense Area Start Here -->
    <div class="card height-auto">
        <div class="card-body">
            <div class="heading-layout1">
                <div class="item-title">
                    <h3>Add New Role</h3>
                </div>
            </div>
            <form class="new-added-form" method="post" action="{{route('admin.roles.store')}}">
                @csrf
                <div class="row">
                    <div class="col-12 form-group">
                        <label>Name *</label>
                        <input type="text" name="name" placeholder="" class="form-control">
                    </div>
                    <div class="row my-5 mx-3">
                        @foreach(\App\Models\Permission::all() as $p)
                            <div class="col-md-3 my-2">
                                <div class="form-check">
                                    <input type="checkbox" value="{{$p->id}}" name="permissions[]" class="form-check-input">
                                    <label class="form-check-label ml-4">{{$p->name}}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn-fill-lg btn-gradient-yellow btn-hover-bluedark">Save</button>
                        <a href="{{route('admin.roles.index')}}" class="btn-fill-lg bg-blue-dark btn-hover-yellow">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection



