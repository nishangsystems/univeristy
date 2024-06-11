@extends('admin.printable')
@section('section')
    @isset($report)
        <div class="py-2">
            <table class="table">
                <thead class="text-capitalize">
                    <tr><th colspan="8" class="header text-center text-dark text-uppercase">{{ $title }}</th></tr>
                    <tr>
                        <th>@lang('text.sn')</th>
                        <th>@lang('text.word_matricule')</th>
                        <th>@lang('text.word_name')</th>
                        <th>@lang('text.word_total')</th>
                        <th>@lang('text.unit_cost')</th>
                        <th>@lang('text.amount_expected')</th>
                        <th>@lang('text.amount_paid')</th>
                        <th>@lang('text.amount_owing')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($report as $rpt)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $rpt->student->matric??'' }}</td>
                            <td>{{ $rpt->student->name??'' }}</td>
                            <td>{{ $rpt->n_courses??'' }}</td>
                            <td>{{ $resit_unit_cost??'' }}</td>
                            <td>{{ intVal($rpt->n_courses??0)*intVal($resit_unit_cost??0) }}</td>
                            <td>{{ intVal($rpt->paid??0)*intVal($resit_unit_cost??0) }}</td>
                            <td>{{ intVal($rpt->unpaid??0)*intVal($resit_unit_cost??0) }}</td>
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