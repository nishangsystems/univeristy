@extends('documentation.layout')
@section('section')
    <ul class="nav nav-list">
        <li>
            <a class="text-capitalize">
                <span class="menu-text text-capitalize h4">
                        <b>{{ $parent->name }}</b>
                </span>
            </a>
        </li>
        @foreach ($data as $doc)
        <li>
            <a href="{{route('documentation.show', $doc->id)}}" class="ml-5 text-capitalize">
                <san class="fa fa-arrow-right menu-icon"></san>
                <span class="menu-text text-capitalize text-decoration-none">
                        {{ $doc->title }}
                </span>
            </a>
        </li>
        @endforeach
    </ul>
@endsection