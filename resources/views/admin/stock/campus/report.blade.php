@extends('admin.layout')
@section('section')
@php($year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
<div class="py-3">
    <form method="get" id="xyz_form">
        <div class="form-group-merged d-flex border-secondary">
            <label class="fw-bold text-capitalize col-sm-4 col-md-4">{{__('text.academic_year')}}</label>
            <div class="col-sm-8 col-md-8">
                <select name="year" id="year" class="form-control" onchange="$('#xyz_form').submit()">
                
                @foreach(\App\Models\Batch::all() as $y)
                    <option value="{{$y->id}}" {{$y->id == $year ? 'selected' : ''}}>{{$y->name}}</option>
                @endforeach
                </select>
            </div>
        </div>
    </form>
    <div class="h4 text-uppercase text-center text-dark">{{$title .' FOR '.\App\Models\Batch::find($year)->name}}</div>
    <table class="table my-3">
        <thead>
            <tr class="">
                <th class="text-center header text-uppercase border-left border-right border-2 border-seconady" colspan="4">Internal Transactions</th>
            </tr>
            <tr class="text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.word_quantity')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_type')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_date')}}</th>
            </tr>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach ($internal_transfers as $transfer)
                <tr class="border-bottom border-secondary">
                    <td class="border">{{$k++}}</td>
                    <td class="border">{{$transfer->quantity}}</td>
                    <td class="border">{{$transfer->type}}</td>
                    <td class="border">{{$transfer->created_at}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="table my-3">
        <thead>
            <tr class="">
                <th class="text-center header text-uppercase border-left border-right border-2 border-seconady" colspan="5">External Transactions</th>
            </tr>
            <tr class="text-capitalize">
                <th>###</th>
                <th>{{__('text.word_name')}}</th>
                <th>{{__('text.word_class')}}</th>
                <th>{{__('text.word_quantity')}}</th>
                <th>{{__('text.word_date')}}</th>
            </tr>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($external_transfers->where('year_id', '=', $year) as $item)
                <tr class="border-bottom border-secondary">
                    <td class="border">{{$k++}}</td>
                    <td class="border">{{' [ '.$item->student->matric.' ] '.$item->student->name}}</td>
                    <td class="border">{{$item->student->_class($year)->name()}}</td>
                    <td class="border">{{$item->quantity}}</td>
                    <td class="border">{{$item->created_at}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex my-3 justify-content-end pr-3">
        <a href="{{Request::url()}}/print" class="btn btn-sm btn-primary">{{__('text.word_print')}}</a>
    </div>
</div>
@endsection
@section('script')
<script>
    function redirect(event) {
        val = event.target.value;
        url = "{{Request::url()}}";
        url = url.replace('__VAL__', val);
        window.location = url;
    }
</script>
@endsection