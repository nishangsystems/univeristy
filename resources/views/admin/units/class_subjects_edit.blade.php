@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.units.class_subjects.update',  [$parent->id])}}">
            @csrf
            @method('PUT')
            <div class="form-group row">
                <label for="cname" class="control-label col-sm-2">Name: </label>
                <div class="col-sm-10">
                    <input for="cname" class="form-control" name="name" value="{{$subject->name}}"></input>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2">Coefficient:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" name="coef" value="{{$subject->coef}}"></input>
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-sm btn-primary mx-3" type="submit">Save</button>
                    <a class="btn btn-sm btn-danger" type="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection