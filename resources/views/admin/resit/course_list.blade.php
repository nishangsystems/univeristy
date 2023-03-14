@extends('admin.layout')
@section('section')
<div class="py-3 d-flex justify-content-end">
    <a class="btn btn-sm btn-primary rounded text-capitalize" href="{{Request::url()}}?print=1" target="new">{{__('text.download_statistics')}}</a>
</div>
<div class="py-2">
    <table class="table">
        <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.course_code')}}</th>
                <th class="border-left border-right border-white">{{__('text.course_title')}}</th>
                <!-- <th class="border-left border-right border-white">{{__('text.credit_value')}}</th> -->
                <th class="border-left border-right border-white">{{__('text.no_of_students')}}</th>
                <th class="border-left border-right border-white"></th>
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
                    <td class="border-left border-right border-white">
                        <a class="btn btn-sm btn-primary" href="{{route('admin.resits.course_list.download', [$element->resit_id, $element->id])}}">{{__('text.word_students')}}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@section('script')
<script>
    function changed(){
        val = $('#search_course').val();
        url = "{{route('search_subjects')}}";
        $.ajax({
            method: 'get',
            data: {'name' : val},
            url: url,
            success: function(data){
                console.log(data);
                count = 1;
                html = '';

                for (let index = 0; index < data.data.length; index++) {
                    const element = data.data[index];
                    // console.log(element);
                    _html = `<tr class="border-bottom border-white">
                        <td class="border-left border-right border-white">${count++}</td>
                        <td class="border-left border-right border-white">${element.code}</td>
                        <td class="border-left border-right border-white">${element.name}</td>
                        <td class="border-left border-right border-white">${element.coef}</td>
                        <td class="border-left border-right border-white">${element.status}</td>
                        <td class="border-left border-right border-white">
                            <a class="btn btn-sm btn-primary" href="{{route('admin.resits.course_list.download', [request('resit_id'), '__CRS__'])}}">{{__('text.word_students')}}</a>
                        </td>
                    </tr>`;
                    
                    html += _html.replace('__CRS__', element.id);
                }
                $('#table_body').html(html);

            }
        })
    }
</script>
@endsection