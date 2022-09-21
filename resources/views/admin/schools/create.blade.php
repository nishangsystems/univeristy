@extends('admin.layout')
@section('section')
    <div class="container form-container col-sm-10 mx-auto">
        <form action="{{route('admin.schools.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row form-group text-capitalize my-3">
                <label for="" class="col-md-2 ">name</label>
                <input type="text" name="name" id="" required class="form-control col-md-10">
            </div>
            <div class="row form-group text-capitalize my-3">
                <label for="" class="col-md-2 ">contact</label>
                <input type="tel" name="contact" required id="" class="form-control col-md-10">
            </div>
            <div class="row form-group text-capitalize my-3">
                <label for="" class="col-md-2 ">address</label>
                <input type="text" name="address" required id="" class="form-control col-md-10">
            </div>
            <div class="row form-group text-capitalize my-3">
                <label for="" class="col-md-2 ">logo</label>
                <input type="file" accept="image/*" name="logo_path" id="" class="form-control col-md-10">
            </div>
            <div class="d-flex justify-content-end py-3 text-capitalize">
                <a href="{{route('admin.schools.index')}}" class="btn btn-primary btn-sm mx-2">cancel</a>
                <button type="submit" class="btn btn-primary btn-sm mx-2">save</button>
            </div>
        </form>
    </div>
@endsection