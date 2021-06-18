@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.expense.update', $expense->id)}}">

            @csrf
            @include('admin.expense.update_form')
            @method('PUT')
        </form>
    </div>
</div>
@endsection