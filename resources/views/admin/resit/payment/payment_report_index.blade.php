@extends('admin.layout')
@section('section')
    <div class="container-fluid">
        <form target="new" id="indexerformid">
            <div class="row">
                <div class="col-md-5 py-2">
                    <select class="form-control rounded" name="year_id" id="yearid">
                        <option value=""></option>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 py-2">
                    <select class="form-control rounded" name="class_id" id="classid">
                        <option value=""></option>
                        @foreach ($classes as $class)
                            <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                        @endforeach
                    </select>
                    
                </div>
                <input type="hidden" name="print" value="1">
                <div class="col-md-2 d-flex justify-content-end py-2">
                    <button class="btn btn-sm btn-primary rounded" type="button" onclick="submit_form()">@lang('text.word_next')</button>
                </div>
            </div>
        </form>
    </div>
    @isset($report)
        <div class="">
            <table class="table">
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

        let submit_form = ()=>{
            let year = $('#yearid').val();
            let _class = $('#classid').val();
            let url = "{{ route('admin.resits.payments.report', ['resit_id'=>$resit->id, 'year_id'=>'__YID__', 'class_id'=>'__CLID__']) }}?print=1".replace('__YID__', year).replace('__CLID__', _class);
            // let form = $('#indexerformid');
            // set form action and submit form
            window.location = url
        }
    </script>
@endsection