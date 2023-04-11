@extends('teacher.layout')
@section('section')
@php
    $request = request();

@endphp
<div class="py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-middle align-content-center py-2">
        <form action="{{Request::url()}}" id="switch_form">
            <input type="hidden" name="campus_id" value="{{request('campus_id')}}">
            <div class="d-flex w-auto text-capitalize">
                <h4 class="my-2">{{__('text.class_list')}}</h4>
                        
                <div class="onoffswitch d-flex justify-content-between mx-3 px-1 py-0" style="width: 9.6rem; height:fit-content; border-radius: 3rem;">
                    <input type="radio" name="switch" class="myonoffswitch" value="false" style="width: 2.2rem; height: 2.2rem; color:blue; border-radius:50%;" {{request('switch')!='true' ? 'checked' : ''}}>
                    <input type="radio" name="switch" class="myonoffswitch" value="true" style="width: 2.2rem; height: 2.2rem; color:blue; border-radius:50%;" {{request('switch')=='true' ? 'checked' : ''}}>
                </div>
                <h4 class="my-2">{{__('text.course_list')}}</h4>
                @if (request('switch') == 'true')
                    <div class="d-flex flex-wrap w-auto text-capitalize text-primary border-left border-right mx-3">
                        @foreach(array_unique($students->pluck('class_id')->toArray()) as $class_id)
                            <div class="d-flex mx-3">
                                <input type="radio" class="mr-3 class_switch" name="class" value="{{$class_id}}" style="width: 2.2rem; height: 2.2rem;">
                                <label class="h4 my-2">{{\App\Models\ProgramLevel::find($class_id)->name()}}</label>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </form>
        <a href="{{route('user.subject.result_template', ['campus_id'=>$request->campus_id, 'class_id'=>$request->class_id, 'course_id'=>$request->course_id])}}" class="btn btn-sm btn-primary text-capitalize">{{__('text.results_template')}}</a>
    </div>
    <div class="adv-table table-responsive ">
        <table cellpadding="0" cellspacing="0" class="table">
            <thead class="text-capitalize bg-light">
                <th>{{__('text.sn')}}</th>
                <th>{{__('text.word_matricule')}}</th>
                <th>{{__('text.word_name')}}</th>
                <th></th>
            </thead>
            <tbody>
                @if (request('switch') == 'true')
                    @foreach(array_unique($students->pluck('class_id')->toArray()) as $class_id)
                        @php($k = 1)
                        <tr><td></td><td></td><td class="h4 text-uppercase py-2 border-top border-bottom"><b>{{\App\Models\ProgramLevel::find($class_id)->name()}}</b></td><td></td></tr>
                        @foreach ($students->where('class_id', $class_id) as $student)
                            <tr>
                                <td>{{$k++}}</td>
                                <td>{{$student->matric}}</td>
                                <td>{{$student->name}}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    @php($k = 1)
                    @foreach ($students as $student)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$student->matric}}</td>
                            <td>{{$student->name}}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('script')
    <script>
        $('.myonoffswitch').on('input', function(){
            $('#switch_form').submit();
        });
        $('.class_switch').on('input', function(){
            window.location = `{{route('user.subject.students', ['class_id'=>'__CL_ID__', 'course_id'=>request('course_id')])}}?campus_id={{request('campus_id')}}`.replace('__CL_ID__', $(this).val());
        });
    </script>
@endsection