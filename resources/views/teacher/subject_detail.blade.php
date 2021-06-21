@extends('teacher.layout')

@section('section')

<div class="container m-t-5">

    <div class="row mt-5">
        <div class="col-6 well well-sm">
            <h5>Title: {{$subject->name}}</h5>
            <h5>Subject Code: {{$subject->code}}</h5>
            <h5>Coefficient: {{$subject->coef}}</h5>
            <h5>Subject Notes: <a href="{{route('user.subject.index', [$subject->id])}}">View Notes </a> </h5>

        </div>
        <div class="col-6">
            <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{route('user.subject.store', [$subject->id])}}">
                <!-- @crsf -->
                <div class="col-md-6">
                    <input type="file" name="file" placeholder="Choose file" id="file">
                    @error('file')
                    <div class="alert alert-danger mt-2 mb-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <button class="btn btn-md btn-primary mx-3" type="submit" id="upload_note">Upload Note</button>
                </div>
                @csrf
            </form>
        </div>
    </div>

</div>

<script>

</script>
@endsection