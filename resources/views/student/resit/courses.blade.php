@extends('student.printable')
@section('section')
<div class="py-1">

    <div class="py-2 text-center h4">{{'[ '.auth('student')->user()->matric.' ] - '.auth('student')->user()->name}}</div>
    <table class="table">
        <thead class="text-capitalize bg-secondary">
            <th class=" border-left border-right border-light text-dark">#</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.course_code')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.course_title')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.credit_value')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.word_status')}}</th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach ($courses as $course)
                <tr class="text-capitalize bg-light border-bottom border-white">
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{$course->subject->code}}</td>
                    <td class="border-left border-right border-white">{{$course->subject->name}}</td>
                    <td class="border-left border-right border-white">{{$course->subject->coef}}</td>
                    <td class="border-left border-right border-white">{{$course->subject->status}}</td>
                </tr>
            @endforeach
            <tr class="border-top border-2 border-success text-capitalize" style="text-align:left; text-transform:capitalize; border: 0.5rem 0 solid black; margin-block: 1rem;">
                <td colspan="3">{{__('text.word_course').' '.__('text.word_total')}}</td>
                <th colspan="2">{{$courses->count()}}</th>
            </tr>
            <tr class="border-top border-2 border-success text-capitalize" style="text-align:left; text-transform:capitalize; border: 0.5rem 0 solid black; margin-block: 1rem;">
                <td colspan="3">{{__('text.unit_cost')}}</td>
                <th colspan="2">{{$unit_cost}}</th>
            </tr>
            <tr class="border-top border-2 border-success text-capitalize" style="text-align:left; text-transform:capitalize; border: 0.5rem 0 solid black; margin-block: 1rem;">
                <td colspan="3">{{__('text.total_cost')}}</td>
                <th colspan="2">{{$total_cost}}</th>
            </tr>
            <tr class="border-top border-2 border-success text-capitalize" style="text-align:left; text-transform:capitalize; border: 0.5rem 0 solid black; margin-block: 1rem;">
                <td colspan="3">{{__('text.amount_paid')}}</td>
                <th colspan="2">{{$amount_paid}}</th>
            </tr>
        </tbody>
        <tfoot class="bg-light py-3">
            <th class=" border-top text-dark" colspan="5"> RESIT FORM | {{ $resit->name??'' }} | {{ $resit->year->name??'' }} </th>
        </tfoot>
    </table>
</div>
@endsection
@section('script')
    <script>
        $(window).on('load', function(){
            window.print();
        })
    </script>
@endsection
