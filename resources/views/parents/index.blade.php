@extends('parents.layout')
@section('section')

<div class="col-sm-12">
    <div class="row d-flex flex-wrap justify-content-center">
        @foreach ($children as $child)
            <div class="col-sm-10 col-md-5 col-lg-3 text-center py-4 px-2 mx-4 my-4" style="border-radius: 1rem !important; box-shadow: -1px -1px #ddd, 1px 1px #ddd !important;">
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