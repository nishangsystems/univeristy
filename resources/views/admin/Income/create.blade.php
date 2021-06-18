@extends('admin.layout')
@section('title', 'Create Income')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.income.store')}}">
            <h5 class="mt-5 font-weight-bold mb-5">Create School Income</h5>
            @csrf
            @include('admin.Income.form')
        </form>
    </div>
</div>
@endsection