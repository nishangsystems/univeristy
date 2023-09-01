@extends('parents.layout')
@section('section')

<div class="col-sm-12">
    <table class="table table-stripped">
        <thead class="text-capitalize">
            <th class="border-left border-right border-dark">###</th>
            <th class="border-left border-right border-dark">{{ __('text.word_name') }}</th>
            <th class="border-left border-right border-dark">{{ __('text.word_class') }}</th>
            <th class="border-left border-right border-dark"></th>
        </thead>
        <tbody>
        @php 
            $k = 1; 
            $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear(); 
        @endphp
            @foreach ($children as $child)
                <tr class="">
                    <td class="border-left border-right border-light">{{ $k++ }}</td>
                    <td class="border-left border-right border-light">{{ $child->name }}</td>
                    <td class="border-left border-right border-light">{{ $child->_class($year??null)->name()??'' }}</td>
                    <td class="border-left border-right border-light">
                        <a href="{{ route('parents.results', $child->id) }}" class="btn btn-xs btn-primary my-1 mx-1">{{ __('text.word_results') }}</a>|
                        <a href="{{ route('parents.fees', $child->id) }}" class="btn btn-xs btn-success my-1 mx-1">{{ __('text.word_fees') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection