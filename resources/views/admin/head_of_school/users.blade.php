@extends('admin.layout')
@section('section')
    <div class="my-3">
        <div class="py-2 d-flex my-2 justify-content-end">
            <a class="btn btn-primary rounded text-capitalize" href="{{ route('admin.headOfSchools.create') }}">@lang('text.new_head_of_school')</a>
        </div>
        <table class="table adv-table">
            <thead class="text-capitalize fw-semibold">
                <th>#</th>
                <th>@lang('text.word_name')</th>
                <th>@lang('text.word_email')</th>
                <th>@lang('text.word_school')</th>
                <th>@lang('text.word_status')</th>
                <th></th>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $k++ }}</td>
                        <td>{{ $user->name??'--user-name--' }}</td>
                        <td>{{ $user->email??'' }}</td>
                        <td>{{ $user->school_name??'' }}</td>
                        <td>{{ $user->status == 1 ? 'CURRENT' : 'PASSIVE' }}</td>
                        <td>
                            @if($user->status == 1)
                                <a class="btn btnxs btn-warning" onclick="window.location = confirm(`Are you sure you want to disactivate {{ $user->name }} as Head of School for SCHOOL OF {{ $user->school_name }}? Confirm to continue.`) ? `{{ route('admin.headOfSchools.setStatus', [$user->hos_id, 0]) }}` : '#'">@lang('text.word_disactivate')</a>
                            @else
                                <a class="btn btnxs btn-success" onclick="window.location = confirm(`Are you sure you want to activate {{ $user->name }} as Head of School for SCHOOL OF {{ $user->school_name }}? Confirm to continue.`) ? `{{ route('admin.headOfSchools.setStatus', [$user->hos_id, 1]) }}` : '#'">@lang('text.word_activate')</a>
                            @endif
                            <a class="btn btnxs btn-danger" onclick="window.location = confirm(`Are you sure you want to delete {{ $user->name }} as Head of School for SCHOOL OF {{ $user->school_name }}? Confirm to continue.`) ? `{{ route('admin.headOfSchools.delete', [$user->hos_id]) }}` : '#'">@lang('text.word_delete')</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection