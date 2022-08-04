@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.scholarship.award', $student->id)}}">

            @csrf

            <div class="form-group @error('amount') has-error @enderror mt-5">
            <label for="cname"  class="control-label col-lg-2">Amount <span style="color: red;">*</span></label>
                <div class="col-lg-10">
                <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required />
                @error('amount')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                </div>
            </div>
             
            <div class="form-group @error('year') has-error @enderror mt-4">
                <label class="control-label col-lg-2">Year  <span style="color: red;">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="year">
                        <option value="">Select year</option>
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