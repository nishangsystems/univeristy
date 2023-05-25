@extends('admin.layout')
@section('section')
<div class="py-4">
    <form method="post" enctype="multipart/form-data">
        @csrf
        <div class="text-center py-3 h5 text-info bg-light"><strong>{{__('text.jpeg_required_phrase')}}</strong></div>
        <div class="input-group border border-secondary rounded my-3 text-capitalize">
            <span class="input-group-text bg-light fw-bolder col-sm-3 col-md-2">{{__('text.word_file')}}</span>
            <input type="file" name="file" accept="image/jpeg" accep id="" class="form-control text-uppercase" required>
            <input type="submit" value="{{__('text.word_save')}}">
        </div>
    </form>
</div>
@endsection