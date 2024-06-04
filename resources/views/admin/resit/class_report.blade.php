@extends('admin.layout')
@section('section')

<div class="py-3">
    <div class="container row shadow py-5 px-3 mb-4 rounded">
        <div class="col-lg-9">
            <select class="rounded form-control" name="class_id" id="class_id_field">
                <option value=""></option>
                @foreach ($classes as $cls)
                    <option value="{{ $cls['id'] }}">{{ $cls['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3">
            <button class="rounded btn btn-primary px-4" onclick="submitClass(this)">@lang('text.word_next')</button>
        </div>
    </div>
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
                            <td>{{ ($rpt->n_courses??0)*intVal($resit_unit_cost??0) }}</td>
                            <td>{{ ($rpt->paid??0)*intVal($resit_unit_cost??0) }}</td>
                            <td>{{ ($rpt->unpaid??0)*intVal($resit_unit_cost??0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endisset
</div>
@endsection
@section('script')
    <script>
        let submitClass = (element)=>{
            let _class = $('#class_id_field').val();
            let _url = "{{ route('admin.resits.class_report', ['resit_id'=>$resit->id, 'class_id'=>'__CLID__']) }}".replace('__CLID__', _class);
            window.location = _url;
        }
    </script>
@endsection