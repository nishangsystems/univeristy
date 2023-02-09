@extends('admin.layout')
@section('section')
    <div class="my-3">
        <form method="post">
            @csrf
            <div class="row my-2">
                <label class="col-sm-3 col-md-3 text-capitalize">{{__('text.word_mode')}}</label>
                <div class="col-sm-9 col-md-9">
                    <select class="form-control" name="mode">
                        <option></option>
                        @foreach (\App\Models\TranscriptRating::modes() as $mode)
                            <option value="{{$mode}} {{$instance->mode??null === $mode ? 'selected' : ''}}">{{$mode}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row my-2">
                <label class="col-sm-3 col-md-3 text-capitalize">{{__('text.word_duration')}}({{__('text.in_days')}})</label>
                <div class="col-sm-9 col-md-9">
                    <input class="form-control" name="duration" type="number" value="{{$instance->duration ?? ''}}">
                </div>
            </div>
            <div class="row my-2">
                <label class="col-sm-3 col-md-3 text-capitalize">{{__('text.current_student_price')}}</label>
                <div class="col-sm-9 col-md-9">
                    <input class="form-control" name="current_price" type="number" value="{{$instance->current_price ?? ''}}">
                </div>
            </div>
            <div class="row my-2">
                <label class="col-sm-3 col-md-3 text-capitalize">{{__('text.former_student_price')}}</label>
                <div class="col-sm-9 col-md-9">
                    <input class="form-control" name="former_price" type="number" value="{{$instance->former_price ?? ''}}">
                </div>
            </div>
            <div class="d-flex justify-content-end my-2">
                <button type="submit" class="btn btn-primary btn-sm">{{__('text.word_save')}}</button>
            </div>
        </form>
    </div> 

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
                            <a class="btn btn-sm btn-primary" href="{{route('admin.res_and_trans.transcripts.config.edit', $rtx->id)}}">{{__('text.word_edit')}}</a>
                            <a class="btn btn-sm btn-danger" onclick="confirm(`Your are about to delete transcript configuration for {{$rtx->mode}}`) ? window.location=`{{route('admin.res_and_trans.transcripts.config.delete', $rtx->id)}}` : ''">{{__('text.word_delete')}}</a>
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection