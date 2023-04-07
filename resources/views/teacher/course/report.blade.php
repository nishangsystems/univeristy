@extends('teacher.layout')
@section('section')
    <div class="py-4">
        <form class="form py-5 px-3" method="get">
            <div class="input-group-merge d-flex border rounded">
                <select class="form-control" name="year">
                    <option value="">{{__('text.select_academic_year')}}</option>
                    @foreach (\App\Models\Batch::all() as $yr)
                        <option value="{{$yr->id}}">{{$yr->name}}</option>
                    @endforeach
                </select>
                <select class="form-control" name="semester">
                    <option value="">{{__('text.word_semester')}}</option>
                    @foreach (\App\Models\ProgramLevel::find(request('program_level_id'))->program->background->semesters as $sm)
                        <option value="{{$sm->id}}">{{$sm->name}}</option>
                    @endforeach
                </select>
                <select class="form-control" name="campus">
                    <option value="">{{__('text.word_campus')}}</option>
                    @foreach (\App\Models\Campus::all() as $sm)
                        <option value="{{$sm->id}}">{{$sm->name}}</option>
                    @endforeach
                </select>
                <input class="btn btn-xs btn-primary" type="submit" value="{{__('text.word_get')}}">
            </div>
        </form>

        <table class="table">
            <thead class="border-bottom border-light text-capitalize">
                <th class="border-left border-right border-light">{{__('text.sn')}}</th>
                <th class="border-left border-right border-light">{{__('text.word_course')}}</th>
                <th class="border-left border-right border-light">{{__('text.word_status')}}</th>
                <th class="border-left border-right border-light">{{__('text.credit_value')}}</th>
                <th class="border-left border-right border-light">{{__('text.course_hours')}}</th>
                <th class="border-left border-right border-light">{{__('text.hours_covered')}}</th>
                <th class="border-left border-right border-light">{{__('text.lessons_taught')}}</th>
                <th class="border-left border-right border-light">{{__('text.per_coverage')}}</th>
                <th class="border-left border-right border-light">{{__('text.per_hours')}}</th>
            </thead>
            <tbody>
                @php
                    $k = 1;
                @endphp
                @foreach ($data as $d)
                    <tr class="border-bottom border-light text-capitalize">
                        <td class="border-left border-right border-light">{{$k++}}</td>
                        <td class="border-left border-right border-light">{{$d['name']}}</td>
                        <td class="border-left border-right border-light">{{$d['status']}}</td>
                        <td class="border-left border-right border-light">{{$d['cv']}}</td>
                        <td class="border-left border-right border-light">{{$d['hours']}}</td>
                        <td class="border-left border-right border-light">{{$d['hours_covered']}}</td>
                        <td class="border-left border-right border-light">{{$d['topics_taught']}}/{{$d['topics']}}</td>
                        <td class="border-left border-right border-light">
                            @if ($d['topics'] > 0)
                                {{number_format(($d['topics_taught'] / $d['topics']) * 100, 2)}}%
                            @endif
                        </td>
                        <td class="border-left border-right border-light">
                            @if ($d['hours'] > 0)
                                {{number_format(($d['hours_covered'] / $d['hours']) * 100, 2)}}%
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection