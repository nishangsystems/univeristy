@extends('teacher.layout')
@section('section')
@php
    $request = request();

@endphp
<div class="py-5">
    <div class="d-flex flex-wrap justify-content-between py-2">
        <form action="{{Request::url()}}" id="switch_form">
            <input type="hidden" name="campus_id" value="{{request('campus_id')}}">
            <div class="d-flex w-auto text-capitalize">
                <h4 class="my-2">{{__('text.class_list')}}</h4>
                        
                <div class="onoffswitch d-flex justify-content-between mx-3 px-1 py-0" style="width: 9.6rem; height:fit-content; border-radius: 3rem;">
                    <input type="radio" name="switch" class="myonoffswitch" value="false" style="width: 2.2rem; height: 2.2rem; color:blue; border-radius:50%;" {{request('switch')=='false' ? 'checked' : ''}}>
                    <input type="radio" name="switch" class="myonoffswitch" value="true" style="width: 2.2rem; height: 2.2rem; color:blue; border-radius:50%;" {{request('switch')=='true' ? 'checked' : ''}}>
                </div>
                <h4 class="my-2">{{__('text.course_list')}}</h4>
                    
            </div>
        </form>
        <a href="{{route('user.subject.result_template', ['campus_id'=>$request->campus_id, 'class_id'=>$request->class_id, 'course_id'=>$request->course_id])}}" class="btn btn-sm btn-primary text-capitalize">{{__('text.results_template')}}</a>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table class="table">
                <thead class="text-capitalize bg-light">
                    <th>{{__('text.sn')}}</th>
                    <th>{{__('text.word_matricule')}}</th>
                    <th>{{__('text.word_name')}}</th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach($students as $student)
                    <tr>
                        <td>{{$k++}}</td>
                        <td>{{$student->matric}}</td>
                        <td>{{$student->name}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        $('.myonoffswitch').on('input', function(){
            $('#switch_form').submit();
        });
    </script>
@endsection