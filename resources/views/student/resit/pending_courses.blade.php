@extends('student.layout')
@section('section')
<div class="py-1">

    <table class="table">
        <thead class="text-capitalize bg-secondary">
            <th class=" border-left border-right border-light text-dark">#</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.course_code')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.course_title')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.credit_value')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.word_status')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.ca_score')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.exam_score')}}</th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach ($courses as $course)
                <tr class="text-capitalize bg-light border-bottom border-white">
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{$course->code}}</td>
                    <td class="border-left border-right border-white">{{$course->name}}</td>
                    <td class="border-left border-right border-white">{{$course->coef}}</td>
                    <td class="border-left border-right border-white">{{$course->status}}</td>
                    <td class="border-left border-right border-white">{{$course->ca_score}}</td>
                    <td class="border-left border-right border-white">{{$course->exam_score}}</td>
                </tr>
            @endforeach
        </tbody>
        {{-- <tfoot class="bg-light text-capitalize">
            <th class=" border-left border-right border-light text-dark">{{__('text.word_course').' '.__('text.word_total')}} : {{$courses->count()}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.unit_cost')}} : {{$unit_cost}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.total_cost')}} : {{$total_cost}}</th>
        </tfoot> --}}
    </table>
</div>
@endsection
