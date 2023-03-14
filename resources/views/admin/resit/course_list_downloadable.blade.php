@extends('admin.printable')
@section('section')
<div class="py-2">
    <table class="table">
        <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.course_code')}}</th>
                <th class="border-left border-right border-white">{{__('text.course_title')}}</th>
                <!-- <th class="border-left border-right border-white">{{__('text.credit_value')}}</th> -->
                <th class="border-left border-right border-white">{{__('text.no_of_students')}}</th>
        </thead>
        <tbody id="table_body">
            @php
                $count = 1;
                $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            @endphp
            @foreach ($courses as $element)
                <tr class="border-bottom border-white">
                    <td class="border-left border-right border-white">{{$count++}}</td>
                    <td class="border-left border-right border-white">{{$element->code}}</td>
                    <td class="border-left border-right border-white">{{$element->name}}</td>
                    <!-- <td class="border-left border-right border-white">{{$element->coef}}</td> -->
                    <td class="border-left border-right border-white">{{$element->student_subjects()->where(['year_id'=>$year, 'resit_id'=>request('resit_id')])->join('students', ['students.id'=>'student_courses.student_id'])->where(function($q){
                        auth()->user()->campus_id == null ? null : 
                        $q->where(['students.campus_id'=>auth()->user()->campus_id]);
                    })->distinct()->count()}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
