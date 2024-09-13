<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <div class="card border-0 rounded shadow-sm">
        <div class="card-header">
            <h3 class="text-uppercase text-primary my-2">&Rang; @lang('text.unvalidated_courses') &Rang; {{$student->name}} &Lang;{{$student->matric}}&Rang;</h3>
        </div>
        <div class="card-body">
            <table class="table-stripped my-4">
                <thead class="text-capitalize border-bottom ">
                    <th class="border-left border-right">@lang('text.sn')</th>
                    <th class="border-left border-right">@lang('text.course_title')</th>
                    <th class="border-left border-right">@lang('text.course_code')</th>
                    <th class="border-left border-right">@lang('text.ca_score')</th>
                    <th class="border-left border-right">@lang('text.exam_score')</th>
                    <th class="border-left border-right">@lang('text.word_total')</th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach ($courses as $course)
                        <tr class="border-bottom">
                            <td class="border-left border-right">{{$k++}}</td>
                            <td class="border-left border-right">{{$course->name}}</td>
                            <td class="border-left border-right">{{$course->code}}</td>
                            <td class="border-left border-right">{{$course->ca_score}}</td>
                            <td class="border-left border-right">{{$course->exam_score}}</td>
                            <td class="border-left border-right">{{$course->total_mark}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
