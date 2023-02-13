@extends('admin.printable')
@section('section')

<div class="py-3">
    <table class="table">
        <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.word_name')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_matricule')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_class')}}</th>
        </thead>
        <tbody id="table_body">
            @php($k = 1)
            @foreach ($subjects as $subject)
                @if ((auth()->user()->campus_id == null) || ($subject->student->campus_id == auth()->user()->campus_id))
                    <tr class="border-bottom border-white">
                        <td class="border-left border-right border-white">{{$k++}}</td>
                        <td class="border-left border-right border-white">{{$subject->student->name}}</td>
                        <td class="border-left border-right border-white">{{$subject->student->matric}}</td>
                        <td class="border-left border-right border-white">{{$subject->student->_class(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name() ?? ''}}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection