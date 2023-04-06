@extends('teacher.layout')
@section('section')
    <div class="py-5">
        <form class="form py-4 my-4" method="get">
            <div class="input-group-merge d-flex border rounded">
                <!-- <select class="form-control" name="campus">
                    <option value="">{{__('text.select_campus')}}</option>
                    @foreach(\App\Models\Campus::all() as $campus)
                        <option value="{{$campus->id}}">{{$campus->name}}</option>
                    @endforeach
                </select> -->
                <input type="month" name="month" class="form-control">
                <input type="submit" class="btn btn-xs btn-primary" value="{{__('text.word_get')}}">
            </div>
        </form>

        @if(request()->has('month') && request('month') != null)
        <table class="table">
            <thead class="text-primary">
                <th class="border-left border-right border-light">{{__('text.sn')}}</th>
                <th class="border-left border-right border-light">{{__('text.word_class')}}</th>
                <th class="border-left border-right border-light">{{__('text.word_course')}}</th>
                <th class="border-left border-right border-light">{{__('text.hours_covered')}}</th>
                <th class="border-left border-right border-light"></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach ($courses->filter(function($el)use($details){
                    return in_array($el->id, $details->pluck('id')->toArray());
                }) as $course)
                    <tr class="border-bottom border-top border-light">
                        <td class="border-left border-right border-light">{{$k++}}</td>
                        <td class="border-left border-right border-light">{{\App\Models\ProgramLevel::find($course->class_id)->name()}}</td>
                        <td class="border-left border-right border-light">{{$course->name}}</td>
                        <td class="border-left border-right border-light">{{$details->filter(function($el)use($course){return $el['id'] == $course->id;})->first()['hours']}}</td>
                        <td class="border-left border-right border-light">
                            <a class="btn btn-xs btn-primary" href="#">{{__('text.word_details')}}</a>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="border-bottom border-top border-primary">
                        <td class="border-left border-right border-light text-uppercase" colspan="3">{{__('text.word_total')}}</td>
                        <td class="border-left border-right border-light" colspan="2">{{array_sum($details->pluck('hours')->toArray())}}</td>
                    </tr>
            </tbody>
        </table>
        @endif
    </div>
@endsection