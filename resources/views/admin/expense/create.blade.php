@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.expense.store')}}">

            @csrf
            @include('admin.expense.form')
        </form>
    </div>
</div>
@endsection