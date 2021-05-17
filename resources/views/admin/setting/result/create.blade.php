@extends('admin.layout')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="mx-3">
        <div class="form-panel">
            <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.result_release.store')}}">
                {{csrf_field()}}
                <p>Create result release and set date range where teachers can edit results</p>
                <div class="form-group @error('year_id') has-error @enderror">
                    <label for="year_id" class="control-label col-lg-2">Year</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="year_id"
                                name="year_id">
                            <option selected disabled>Select Year</option>
                            @foreach(\App\Models\Batch::all() as $batch)
                                <option {{old('year_id') == $batch->id?'selected':''}} value="{{$batch->id}}">{{$batch->name}}</option>
                            @endforeach
                        </select>
                        @error('year_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('seq_id') has-error @enderror">
                    <label for="seq_id" class="control-label col-lg-2">Sequence</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="seq_id"
                                name="seq_id">
                            <option selected disabled>Sequence ID</option>
                            @foreach(\App\Models\Sequence::all() as $sequence)
                                <option {{old('seq_id') == $sequence->id?'selected':''}} value="{{$sequence->id}}">{{$sequence->name}}</option>
                            @endforeach
                        </select>
                        @error('seq_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('end_date') has-error @enderror">
                    <label for="end_date" class="control-label col-lg-2">End Date</label>
                    <div class="col-lg-10">
                        <input id="end_date" class=" form-control" name="end_date" value="{{old('end_date')}}" type="date" required/>
                        @error('end_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('start_date') has-error @enderror">
                    <label for="start_date" class="control-label col-lg-2">Start Date</label>
                    <div class="col-lg-10">
                        <input id="start_date" class=" form-control" name="start_date" value="{{old('start_date')}}" type="date" required/>
                        @error('start_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-xs btn-primary" type="submit">Save</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.result_release.index')}}" type="button">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
