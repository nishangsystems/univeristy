@extends('admin.layout')
@section('section')
<div class="py-3">
    <table class="table">
        <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.word_title')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_background')}}</th>
                <th class="border-left border-right border-white">{{__('text.start_date')}}</th>
                <th class="border-left border-right border-white">{{__('text.end_date')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_status')}}</th>
                <th class="border-left border-right border-white"></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach (\App\Models\Resit::where(function($q){auth()->user()->campus_id == null ? null : $q->where('campus_id', auth()->user()->campus_id);})->orderBy('id', 'DESC')->get() as $resit)
                <tr class="border-bottom border-white">
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{$resit->name??null}}</td>
                    <td class="border-left border-right border-white">{{$resit->background->background_name}}</td>
                    <td class="border-left border-right border-white">{{date('l d-m-Y', strtotime($resit->start_date))}}</td>
                    <td class="border-left border-right border-white">{{date('l d-m-Y', strtotime($resit->end_date))}}</td>
                    <td class="border-left border-right border-white">@if($resit->is_open()) <span class="text-primary">{{__('text.word_open')}}</span> @else <span class="text-danger">{{__('text.word_closed')}}</span> @endif</td>
                    <td class="border-left border-right border-white">
                        <a class="btn btn-sm btn-primary" href="{{route('admin.resits.course_list', $resit->id)}}">{{__('text.word_students')}}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection