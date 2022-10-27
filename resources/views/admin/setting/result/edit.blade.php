@extends('admin.layout')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="mx-3">
        <div class="form-panel">
            <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.result_release.update', $release->id)}}">
                {{csrf_field()}}
                <p>{{__('text.phrase_5')}}</p>
                <input type="hidden" name="_method" value="put">
                <div class="form-group @error('year_id') has-error @enderror">
                    <label for="year_id" class="control-label col-lg-2">Year</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="year_id"
                                name="year_id">
                            <option selected disabled>{{__('text.select_year')}}</option>
                            @foreach(\App\Models\Batch::all() as $batch)
                                <option {{old('year_id',$release->year_id) == $batch->id?'selected':''}} value="{{$batch->id}}">{{$batch->name}}</option>
                            @endforeach
                        </select>
                        @error('year_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('seq_id') has-error @enderror">
                    <label for="seq_id" class="control-label col-lg-2 text-capitalize">{{__('text.word_sequence')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control" id="seq_id"
                                name="seq_id">
                            <option selected disabled>{{__('text.sequence_id')}}</option>
                            @foreach(\App\Models\Sequence::all() as $sequence)
                                <option {{old('seq_id',$release->seq_id) == $sequence->id?'selected':''}} value="{{$sequence->id}}">{{$sequence->name}}</option>
                            @endforeach
                        </select>
                        @error('seq_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group @error('start_date') has-error @enderror">
                    <label for="start_date" class="control-label col-lg-2 text-capitalize">{{__('text.start_date')}}</label>
                    <div class="col-lg-10">
                        <input id="start_date" class=" form-control" name="start_date" value="{{old('start_date',$release->start_date->format('Y-m-d'))}}" type="date" required/>
                        @error('start_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('end_date') has-error @enderror">
                    <label for="end_date" class="control-label col-lg-2 text-capitalize">{{__('text.end_date')}}</label>
                    <div class="col-lg-10">
                        <input id="end_date" class=" form-control" name="end_date" value="{{old('end_date',$release->end_date->format('Y-m-d'))}}" type="date" required/>
                        @error('end_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>    
                </div>    


                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-xs btn-primary" type="submit">{{__('text.word_save')}}</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.result_release.index')}}" type="button">{{__('text.word_cancel')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
