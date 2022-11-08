@extends('admin.layout')
@section('section')
<div class="container-fluid">
    <form  method="post">
        @csrf
        <div class="row py-3">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_matricule')}}</label>
            <div class="col-md-9">
                <input type="text" name="matric" id="" class="form-control" placeholder="matric" required>
            </div>
        </div>
        <div class="row py-3">
            <label for="" class="col-md-3 text-capitalize">{{__('text.academic_year')}}</label>
            <div class="col-md-9">
                <select name="year" id="" class="form-control" required>
                    <option value="">{{__('text.academic_year')}}</option>
                    @foreach(\App\Models\Batch::all() as $batch)
                        <option value="{{$batch->id}}">{{$batch->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row py-3">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_semester')}}</label>
            <div class="col-md-9">
                <select name="semester" id="" class="form-control" required>
                    <option value="">{{__('text.word_semester')}}</option>
                    @foreach(\App\Models\Semester::all() as $sem)
                        <option value="{{$sem->id}}">{{$sem->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end py-3">
            <button type="submit" class="btn btn-sm btn-primary text-uppercase">{{__('text.word_get')}}</button>
        </div>
    </form>
</div>
@endsection
@section('script')
@endsection