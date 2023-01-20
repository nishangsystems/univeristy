@extends('student.layout')
@section('section')
<div class="py-3 row">
    <div class="rounded shadow col-sm-8 mx-auto bg-light my-5 py-0 text-center">
        <div class="header text-center h4 py-2 my-0 text-capitalize">
            <b>{{__('text.payment_details')}}</b>
        </div>
        <div class="my-3">
            <table>
                <thead class="text-capitalize h4 text-light bg-secondary">
                    <th class="border-left border-right border-white">#</th>
                    <th class="border-left border-right border-white">{{__('text.word_course')}}</th>
                    <th class="border-left border-right border-white">{{__('text.word_action')}}</th>
                </thead>
                <tbody class="bg-light">
                    @php($k = 1)
                    @foreach($courses as $course)
                        <tr class="border-bottom border-secondary">
                            <td class="border-left border-right border-white text-left">{{$k++}}</td>
                            <td class="border-left border-right border-white text-left"><span class="text-primary">[ {{$course->code}} ]</span> {{$course->name}}</td>
                            <td class="border-left border-right border-white text-left">
                                <button class="btn btn-sm fa fa-trash btn-danger" onclick="drop_course('{{$course->id}}')">{{__('text.word_drop')}}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="container-fluid h4">
            <p class="text-dark my-2 py-5">
                {{__('text.payment_details_statement', ['qnty'=>$quantity, 'amnt'=>$amount])}}
            </p>
            <div class="d-flex justify-content-between my-5">
                <a href="{{URL::previous()}}" class="text-capitalize btn btn-sm btn-secondary rounded">{{__('text.word_back')}}</a>
                <a href="" class="text-capitalize btn btn-sm btn-primary rounded">{{__('text.word_proceed')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    var courses;
    $(document).ready(function(){
        courses = `{{$courses}}`;
    });
    function drop_course(course_id){
        courses = courses
    }
</script>
@endsection