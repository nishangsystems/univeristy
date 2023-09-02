@extends('parents.layout')
@section('section')

<div class="col-sm-12">
    <div class="row">
        @foreach ($children as $child)
            <div class="col-sm-10 mx-auto col-md-5 col-lg-3 text-center alert alert-info border rounded-md py-4 px-2">
                <h4 class="">{{ $child->name }}</h4>
                <h5 class="my-2">{{ $child->_class($year??null)->name()??'' }}</h5>
                <div class="d-flex flex-wrap justify-content-around mt-5">
                    <a href="{{ route('parents.results', $child->id) }}" class="btn btn-xs btn-primary my-1 mx-1 px-3 py-1 rounded">{{ __('text.word_results') }}</a>
                    <a href="{{ route('parents.fees', $child->id) }}" class="btn btn-xs btn-success my-1 mx-1 px-3 py-1 rounded">{{ __('text.word_fees') }}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection