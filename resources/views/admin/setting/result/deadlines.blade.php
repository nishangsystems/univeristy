@extends('admin.layout')
@section('section')
<div class="py-3">

    <table class="table">
        <thead class="text-capitalize">
            <th></th>
            <th>@lang('text.word_background')</th>
            <th></th>
        </thead>
        @php
            $k = 1;
        @endphp
        @foreach ($backgrounds as $background)
            <tr>
                <td>{{ $k++ }}</td>
                <td>{{ $background->background_name }}</td>
                <td>
                    <a class="btn btn-xs btn-success fw-bold" href="{{ route('admin.result.ca.dateline.set', $background->id) }}">set ca upload deadline</a>
                    <a class="btn btn-xs btn-warning fw-bold" href="{{ route('admin.result.exam.dateline.set', $background->id) }}">set exam upload deadline</a>
                </td>
            </tr>
        @endforeach
    </table>
    

</div>
@endsection