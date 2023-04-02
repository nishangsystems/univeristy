@extends('student.layout')
@section('section')
<div class="py-3">

    <table class="table">
        <thead class="text-capitalize bg-primary">
            <th class=" border-left border-right border-light text-dark">#</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.academic_year')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.start_date')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.end_date')}}</th>
            <th class=" border-left border-right border-light text-dark">{{__('text.word_status')}}</th>
            <th class=" border-left border-right border-light text-dark"></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach (\App\Models\Resit::where(['campus_id'=>auth('student')->user()->campus_id, 'background_id'=>auth('student')->user()->_class()->program->background->id])->get() as $resit)
                <tr class="text-capitalize bg-light border-bottom border-white">
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{$resit->year->name}}</td>
                    <td class="border-left border-right border-white">{{date('l d-m-Y', strtotime($resit->start_date))}}</td>
                    <td class="border-left border-right border-white">{{date('l d-m-Y', strtotime($resit->end_date))}}</td>
                    <td class="border-left border-right border-white">@if($resit->is_open()) <span class="text-primary">{{__('text.word_open')}}</span> @else <span class="text-danger">{{__('text.word_closed')}}</span> @endif</td>
                    <td class="border-left border-right border-white">
                        <a href="{{route('student.resit.download_courses', $resit->id)}}" class="btn btn-sm btn-primary">{{__('text.resit_form')}}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('script')
<script>
    
</script>
@endsection