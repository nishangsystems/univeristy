@extends('admin.layout')
@section('section')
@php($year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
<div class="py-3">
    <div class="row mx-auto mb-4">
        <a class="col-sm-6 col-md-6 col-lg-6 btn btn-primary text-uppercase active" href="{{route('admin.stock.report', 'receivable')}}">{{__('text.receivable_report')}}</a>
        <a class="col-sm-6 col-md-6 col-lg-6 btn btn-success text-uppercase" href="{{route('admin.stock.report', 'givable')}}">{{__('text.givable_report')}}</a>
    </div>
    <table class="table">
        <div class="h4 text-uppercase text-center text-dark">{{request('type') == 'receivable' ? __('text.receivable_report') : (request('type') == 'givable' ? __('text.givable_report') : '')}}</div>
        <thead class="text-capitalize">
            <!-- <th>###</th> -->
            <th>{{__('text.word_item')}}</th>
            <th>{{__('text.word_name')}}</th>
            <th>{{__('text.word_matricule')}}</th>
            <th>{{__('text.word_class')}}</th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach(\App\Models\Stock::where(['type'=>request('type')])->get() as $item)
                @foreach($item->studentStock()->get() as $st_item)
                <tr class="border-bottom border-secondary">
                    <td class="border">{{$item->name}}</td>
                    <td class="border">{{$st_item->student->name}}</td>
                    <td class="border">{{$st_item->student->matric}}</td>
                    <td class="border">{{$st_item->student->_class($year)->name()}}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    <div class="d-flex my-3 justify-content-end pr-3">
        <a href="{{Request::url()}}/print" class="btn btn-sm btn-primary">{{__('text.word_print')}}</a>
    </div>
</div>
@endsection