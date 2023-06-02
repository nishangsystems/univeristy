@extends('documentation.layout')
@section('section')
    <div class="d-flex justify-content-end py-4">
        @if ($item->parent_id != 0)
            <a class="btn btn-xs btn-secondary" href="{{route('documentation.show', [$item->parent_id])}}">{{__('text.word_parent')}}</a>|
        @endif
        <a class="btn btn-xs btn-primary" href="{{route('documentation.edit', [request('id')])}}">{{__('text.word_edit')}}</a>|
        <a class="btn btn-xs btn-success" href="{{route('documentation.create', [request('id')])}}">{{__('text.add_child')}}</a>
    </div>
    <div class="py-5 my-5 card px-5 bg-light">
        <p>{!! $item->content !!}</p>
        @if ($item->children->count() > 0)
            <h3 class="text-capitalize fw-bold py-2">{{__('text.word_content')}}</h3>
            <ul class="nav nav-list">
                @foreach ($item->children as $child)
                    <li>
                        <a class="text-capitalize" href="{{route('documentation.show', [$child->id])}}">{{$child->title}}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection