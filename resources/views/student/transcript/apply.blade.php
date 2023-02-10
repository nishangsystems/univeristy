@extends('student.layout')
@section('section')
    @php
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $current = auth()->user()->_class($year) != null;
    @endphp
    @if (request('config_id') == null)
        <div class="my-2">
            <table class="table">
                <thead class="text-capitalize bg-dark text-white">
                    <th class="border-left border-right border-light">#</th>
                    <th class="border-left border-right border-light">{{__('text.word_mode')}}</th>
                    <th class="border-left border-right border-light">{{__('text.word_duration')}}</th>
                    <th class="border-left border-right border-light">{{__('text.current_student_price')}}</th>
                    <th class="border-left border-right border-light">{{__('text.former_student_price')}}</th>
                    <th class="border-left border-right border-light"></th>
                </thead>
                <tbody>
                    @foreach (\App\Models\TranscriptRating::all() as $rtx)
                        <tr class="border-bottom border-secondary">
                            <th class="border-left border-right border-light">#</th>
                            <th class="border-left border-right border-light">{{$rtx->mode}}</th>
                            <th class="border-left border-right border-light">{{$rtx->duration.' '.trans_choice('text.word_day' ,2)}}</th>
                            <th class="border-left border-right border-light">{{$rtx->current_price.' '.__('text.currency_cfa')}}</th>
                            <th class="border-left border-right border-light">{{$rtx->former_price.' '.__('text.currency_cfa')}}</th>
                            <th class="border-left border-right border-light">
                                <a class="btn btn-sm btn-primary" href="{{route('student.transcript.apply', $rtx->id)}}">{{__('text.word_apply')}}</a>
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else 
        @php($rtx = \App\Models\TranscriptRating::find(request('config_id'))) 
        <div class="py-3">
            <form method="post">
                @csrf
                <div class="row my-2">
                    <label class="col-sm-3 col-md-2 text-capitalize">{{__('text.word_mode')}}</label>
                    <div class="col-sm-9 col-md-10">
                        <input name="config_id" value="{{request('config_id')}}" type="hidden">
                        <label class="form-control text-capitalize">{{$rtx->mode.' ( '.$rtx->duration.trans_choice('text.word_day', 2).' ) - '}} @if($current) {{ $rtx->current_price }} @else {{ $rtx->former_price}} @endif {{__('text.currency_cfa')}}</label>
                    </div>
                </div>
      
                <div class="row my-2">
                    <label class="col-sm-3 col-md-2 text-capitalize">{{__('text.academic_year')}}</label>
                    <div class="col-sm-9 col-md-10">
                        <select name="year_id" class="form-control">
                            <option></option>
                            @foreach (\App\Models\Batch::all() as $btch)
                                <option value="{{$btch->id}}">{{$btch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                
                <div class="row my-2">
                    <label class="col-sm-3 col-md-2 text-capitalize">{{__('text.delivery_format')}}</label>
                    <div class="col-sm-9 col-md-10">
                        <select class="form-control text-uppercase" name="delivery_format" required>
                            <option>---</option>
                            <option value="HARD COPY">{{__('text.hard_copy')}}</option>
                            <option value="SOFT COPY">{{__('text.soft_copy')}}</option>
                        </select>
                    </div>
                </div>
                
                <div class="row my-2">
                    <label class="col-sm-3 col-md-2 text-capitalize">{{__('text.word_phone')}}</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="tel" class="form-control" name="tel" required value="{{auth()->user()->phone ?? ''}}">
                    </div>
                </div>
                
                <div class="row my-2">
                    <label class="col-sm-3 col-md-2 text-capitalize">{{__('text.momo_number')}}</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="tel" class="form-control" name="momo_number" required value="{{auth()->user()->phone ?? ''}}">
                    </div>
                </div>

                
                <div class="row my-2">
                    <label class="col-sm-3 col-md-2 text-capitalize">{{__('text.word_description')}}</label>
                    <div class="col-sm-9 col-md-10">
                        <textarea rows="3" class="form-control" name="description"></textarea>
                    </div>
                </div>


                <div class="d-flex justify-content-end my-w">
                    <button type="submit" class="btn btn-sm btn-primary">{{__('text.word_proceed')}}</button>
                </div>
                
            </form>
        </div>
    @endif
@endsection