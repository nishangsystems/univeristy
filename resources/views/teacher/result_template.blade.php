@extends('teacher.layout')
@section('section')
@php
    $request = request();

@endphp
<div class="adv-table py-3">

    <div class="py-4">
        <form action="{{Request::url()}}" id="switch_form">
            <div class="d-flex w-auto text-capitalize">
                <h4 class="my-2">{{__('text.course_list')}}</h4>
                <div class="onoffswitch d-flex justify-content-between mx-3 px-1 py-0" style="width: 9.6rem; height:fit-content; border-radius: 3rem;">
                    <input type="radio" name="switch" class="myonoffswitch" value="true" style="width: 2.2rem; height: 2.2rem; color:blue; border-radius:50%;" {{request('switch')=='true' ? 'checked' : ''}}>
                    <input type="radio" name="switch" class="myonoffswitch" value="false" style="width: 2.2rem; height: 2.2rem; color:blue; border-radius:50%;" {{request('switch')!='true' ? 'checked' : ''}}>
                </div>
                <h4 class="my-2">{{__('text.class_list')}}</h4>
            </div>
        </form>
    </div>
    <table class="table adv-table">
        <thead class="text-capitalize bg-light">
            <th>{{__('text.word_matricule')}}</th>
            <th>{{__('text.course_code')}}</th>
            <th>{{__('text.ca_mark')}}</th>
            <th>{{__('text.exam_mark')}}</th>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{$student->matric}}</td>
                <td>{{\App\Models\Subjects::find($request->course_id)->code ?? ''}}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@section('script')
    <script>
        $('.myonoffswitch').on('input', function(){
            $('#switch_form').submit();
        });
    </script>
@endsection