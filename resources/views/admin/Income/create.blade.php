@extends('admin.layout')
@section('title', 'Create Income')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.income.store')}}">

            @csrf
            @include('admin.Income.form')
        </form>
    </div>
</div>
@endsection