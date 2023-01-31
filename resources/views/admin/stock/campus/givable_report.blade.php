@extends('admin.printable')

@section('section')
<div class="col-sm-12">
    
    
    <div class="py-3">
        @php
            
            $k = 1;
        @endphp
        <table class="">
            <thead class="text-capitalize bg-light">
                <th class="border-left border-right border-white text-black">###</th>
                <th class="border-left border-right border-white text-black">{{__('text.word_name')}}</th>
                <th class="border-left border-right border-white text-black">{{__('text.word_matricule')}}</th>
                <th class="border-left border-right border-white text-black">{{__('text.word_quantity')}}</th>
                <th class="border-left border-right border-white text-black">{{__('text.word_date')}}</th>
                <!-- <th></th> -->
            </thead>
            <tbody>
                @foreach($report->sortBy('student_name') as $stk)
                    <tr class="border-bottom">
                        <td class="border-right border-light">{{$k++}}</td>
                        <td class="border-right border-light">{{$stk->student_name}}</td>
                        <td class="border-right border-light">{{$stk->student_matric}}</td>
                        <td class="border-right border-light">{{$stk->quantity}}</td>
                        <td class="border-right border-light">{{date('l d-m-Y', strtotime($stk->created_at))}}</td>
                        <!-- <td>
                            <a href="{{route('admin.stock.campus.student_stock.delete', [request('campus_id'), $stk->id])}}" class="btn btn-sm btn-danger" onclick="event.preventDefault(); delete_alert(event, '{{"Record for ".\App\Models\Students::find($stk->student_id)->name}}')">{{__('text.word_delete')}}</a>
                        </td> -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('script')

@endsection
