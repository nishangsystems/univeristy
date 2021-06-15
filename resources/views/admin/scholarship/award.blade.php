@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.scholarship.award', $student->id)}}">
            <h5 class="mt-5 font-weight-bold mb-3">Scholarship Award to {{$student->name}}</h5>
            @csrf

            <div class="form-group @error('scholarship_id') has-error @enderror mt-5">
                <label class="control-label col-lg-2">Scholarship Type</label>
                <div class="col-lg-10">
                    <select class="form-control" name="scholarship_id">
                        <option value="" disabled>Select Scholarship Type</option>
                        @foreach($scholarships as $key => $scholarship)
                        <option value="{{$scholarship->id}}">{{$scholarship->name }}, {{$scholarship->type}}</option>
                        @endforeach
                    </select>
                    @error('scholarship_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('year') has-error @enderror mt-4">
                <label class="control-label col-lg-2">Year</label>
                <div class="col-lg-10">
                    <select class="form-control" name="year">
                        <option value="" disabled>Select year</option>
                        @foreach($years as $key => $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">Save</button>
                    <a class="btn btn-xs btn-danger" type="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection