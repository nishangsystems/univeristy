@extends('admin.printable')
@section('section')
    @isset($report)
        <div class="">
            <table class="">
                <thead class="text-capitalize">
                    {{-- <tr><th colspan="9" class="header py-2 text-center text-dark text-uppercase">{{ $title }}</th></tr> --}}
                    <tr class="border-top border-bottom border-dark">
                        <th class="py-2">@lang('text.sn')</th>
                        <th class="py-2">@lang('text.word_matricule')</th>
                        <th class="py-2">@lang('text.word_name')</th>
                        <th class="py-2">@lang('text.word_total')</th>
                        <th class="py-2">@lang('text.unit_cost')</th>
                        <th class="py-2">@lang('text.amount_expected')</th>
                        <th class="py-2">@lang('text.paid_online')</th>
                        <th class="py-2">@lang('text.cash_collected')</th>
                        <th class="py-2">@lang('text.amount_owing')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($report as $rpt)
                        <tr class="border-bottom border-light py-2">
                            <td>{{ $k++ }}</td>
                            <td>{{ $rpt->matric??'' }}</td>
                            <td>{{ $rpt->name??'' }}</td>
                            <td>{{ $rpt->n_courses??'' }}</td>
                            <td>{{ number_format($rpt->unit_cost??0) }}</td>
                            <td>{{ number_format($rpt->expected_amount??0) }}</td>
                            <td>{{ number_format($rpt->paid_online??0) }}</td>
                            <td>{{ number_format($rpt->cash_payment??0) }}</td>
                            <td>{{ number_format(($rpt->expected_amount??0) - ($rpt->paid_online??0) - ($rpt->cash_payment??0)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endisset
@endsection
@section('script')
    <script>

        let printList = ()=>{
            let printable = $('.table');
            let doc = $(document.body).html();
            $(document.body).html(printable);
            window.print();
            $(document.body).html(doc);
        }
    </script>
@endsection