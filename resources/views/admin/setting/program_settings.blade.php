@extends('admin.layout')
@section('section')
<div class="py-3">
    <form method="get" class="my-3">
        <div class="input-group-merge border border-dark rounded d-flex text-capitalize">
            <span class="input-group-text col-sm-2" style="font-size: small;">{{__('text.word_program')}}</span>
            <select name="program" id="" class="form-control" required>
                <option value=""></option>
                @foreach($programs as $prog)
                    <option value="{{$prog->id}}" {{request('program') == $prog->id ? 'selected' : ''}}>{{$prog->name}}</option>
                @endforeach
            </select>
            <input type="submit" name="" value="{{__('text.word_get')}}" id="">
        </div>
    </form>
    @if(request()->has('program'))
        @php($p = \App\Models\SchoolUnits::find(request('program')))
        <form method="post" class="py-3 mt-5 ">
            @csrf
            <input type="hidden" name="program" id="" value="{{request('program')}}">
            
            <div class="input-group-merge border border-dark rounded d-flex text-capitalize my-2">
                <span class="input-group-text col-sm-2" style="font-size: small;">{{__('text.resit_cost')}}</span>
                <input type="number" name="resit_cost" id="" class="form-control" required value="{{$p->resit_cost}}">
            </div>
            <div class="d-flex justify-content-end my-3">
                <input type="submit" value="{{__('text.word_save')}}" class="btn btn-primary btn-sm">
            </div>
        </form>
    @endif
</div>
@endsection