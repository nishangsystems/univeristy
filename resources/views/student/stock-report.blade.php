@extends('student.layout')
@section('section')
@php($year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear())
<div class="py-3">
    <form method="get" >
        <div class="form-group-merged d-flex border-secondary">
            <label class="fw-bold text-capitalize col-sm-4 col-md-4">{{__('text.academic_year')}}</label>
            <div class="col-sm-8 col-md-8">
                <select name="" id="year" class="form-control" onchange="event.preventDefault(); redirect(event)">
                @foreach(\App\Models\Batch::all() as $y)
                    <option value="{{$y->id}}" {{$y->id == $year ? 'selected' : ''}}>{{$y->name}}</option>
                @endforeach
                </select>
            </div>
        </div>
    </form>
    <table class="table">
        <thead class="text-capitalize">
            <!-- <th>###</th> -->
            <th>###</th>
            <th>{{__('text.word_name')}}</th>
            <th>{{__('text.word_quantity')}}</th>
            <th>{{__('text.word_date')}}</th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($stock as $item)
            <tr class="border-bottom border-secondary">
                <td class="border">{{$k++}}</td>
                <td class="border">{{$item->stock->name}}</td>
                <td class="border">{{$item->quantity}}</td>
                <td class="border text-capitalize {{$item->stock->type == 'givable' ? 'text-primary' : 'text-success'}}">{{date('l d-m-Y', strtotime($item->created_at))}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@section('script')
<script>
    function redirect(event) {
        val = event.target.value;
        url = "{{route('student.stock.report', '__VAL__')}}";
        url = url.replace('__VAL__', val);
        window.location = url;
    }
</script>
@endsection