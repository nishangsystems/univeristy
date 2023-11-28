@extends('admin.layout')
@section('section')
    <div class="card">
        <div class="card-body">
            <table class="table adv-table">
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{ __('text.word_class') }}</th>
                    <th>{{ __('text.word_year') }}</th>
                    <th>{{ __('text.word_fee') }}</th>
                    <th>{{ __('text.extra_fee') }}</th>
                    <th>{{ __('text.word_scholarship') }}</th>
                    <th>{{ __('text.word_paid') }}</th>
                </thead>
                <tbody>
                @php
                    $k = 1;
                @endphp
                    @foreach ($classes as $class)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $class->name() }}</td>
                            <td>{{ \App\Models\Batch::find($class->year_id)->name??'' }}</td>
                            <td>{{ $fee->where('year_id', '=', $class->year_id)->sum('fee') }}</td>
                            <td>{{ $extra_fee->where('year_id', '=', $class->year_id)->sum('amount') }}</td>
                            <td>{{ $scholarship->where('batch_id', '=', $class->year_id)->sum('amount') }}</td>
                            <td>{{ $payments->where('payment_year_id', '=', $class->year_id)->sum('amount') - $payments->where('payment_year_id', '=', $class->year_id)->sum('debt')}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection