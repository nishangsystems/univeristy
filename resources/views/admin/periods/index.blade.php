@extends('admin.layout')
@section('section')
    <div class="py-2">
        <form method="post" class="container">
            @csrf
            <div class="row">
                <div class="col-md-5">
                    <span class="text-secondary fw-semibold text-capitalize">@lang('text.starts_at'):</span>
                    <input class="form-control" placeholder="start time" type="time" name="starts_at" value="{{ old('starts_at') }}" required>
                </div>
                <div class="col-md-5">
                    <span class="text-secondary fw-semibold text-capitalize">@lang('text.ends_at'):</span>
                    <input class="form-control" placeholder="end time" type="time" name="ends_at" value="{{ old('ends_at') }}" required>
                </div>
                <div class="col-md-2 d-flex justify-content-end py-2">
                    <input class="btn btn-sm btn-primary" value="@lang('text.word_create')" type="submit">
                </div>
            </div>
        </form>
        <div class="py-3">
            <table class="table">
                <thead class="text-capitalize">
                    <th>#</th>
                    <th>@lang('text.starts_at')</th>
                    <th>@lang('text.ends_at')</th>
                    <th></th>
                <thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($periods as $period)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $period->starts_at }}</td>
                            <td>{{ $period->ends_at }}</td>
                            <td>
                                <a class="btn btn-primary btn-xs" href="{{ route('admin.periods.edit', $period->id) }}">@lang('text.word_edit')</a>
                                <a class="btn btn-danger btn-xs" onclick="window.location = confirm('You are about to delete a period. This operation can not be reversed')? `{{ route('admin.periods.delete', $period->id) }}` : '#'">@lang('text.word_delete')</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection