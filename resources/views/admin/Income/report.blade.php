@extends('admin.layout')
@section('title', 'Eligible incomes')
@section('section')

<div class="col-sm-12">
    <div class="content-panel">
        <!-- <div class="d-flex justify-content-end my-3">
            <a class="btn btn-primary rounded text-capitalize" href="{{ Request::url() }}/print">@lang('text.word_print')</a>
        </div> -->
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.income_type')}}</th>
                        <th>{{__('text.word_amount')}} ({{__('text.currency_cfa')}})</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th>{{__('text.word_class')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $k=>$income)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$income->student->name}}</td>
                        <td>{{$income->income->name}}</td>
                        <td>{{number_format($income->amount != null ? $income->amount : $income->income->amount)}}</td>
                        <td>{{$income->student->campus->name??''}}</td>
                        <td>{{\App\Models\ProgramLevel::find($income->class_id)->name()}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection