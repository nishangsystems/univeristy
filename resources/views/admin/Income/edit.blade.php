@extends('admin.layout')
@section('title', 'Update Income')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.income.update', $income->id)}}">
            <h5 class="mt-5 font-weight-bold mb-5">Update School Income</h5>
            @csrf
            @include('admin.Income.form')
            @method('PUT')
        </form>
    </div>
</div>
@endsection