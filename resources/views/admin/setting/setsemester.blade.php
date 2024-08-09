@extends('admin.layout')
@section('section')
<div class="py-3">
    <table class="table">
        <thead class="text-capitalize">
            <th>{{__('text.sn')}}</th>
            <th>{{__('text.word_background')}}</th>
            <th>{{__('text.word_semester')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($semesters ?? [] as $sem)
                <tr>
                    <td class="border-left border-right border-light">{{$k++}}</td>
                    <td class="border-left border-right border-light">{{$sem->background_name}}</td>
                    
                    <td class="border-left border-right border-light">{{$sem->name}}</td>
                    <td class="border-left border-right border-light">
                        <form action="{{route('admin.postsemester', [$sem->id])}}" method="post" id="form-{{$sem->id}}">@csrf
                            <button type="submit" class="btn {{$sem->status == 1 ? 'btn-warning' :'btn-primary'}} btn-md">{{$sem->status == 1 ? __('text.current_semester') :__('text.set_semester')}}</button>
                            <input type="hidden" name="background" id="" value="{{$sem->background_id}}">
                        </form>
                        @if($sem->status == 1)
                            <a href="{{route('admin.semesters.result.set_datelines', $sem->id)}}" class="btn btn-success btn-md">{{ __('text.result_datelines')}}</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection