@extends('admin.layout')
@section('title', 'Update Income')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.income.update', $income->id)}}">

            @csrf
            @include('admin.Income.update_form')
            @method('PUT')
        </form>
    </div>
</div>
@endsection