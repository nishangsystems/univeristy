@extends('admin.layout')
@section('section')
@php
    $year = request()->has('year') ? request('year') : \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $k = 1;
@endphp
<div class="py-3">
    <div id="picker_form" name="picker_form">
        <form method="get">
            <div class="d-flex input-group-merge border border-dark rounded my-3">
                <label for="" class="input-group-text px-3 text-center bg-dark text-light ">{{__('text.word_year')}}</label>
                <select name="year" class="form-control" required>
                    <option value=""></option>
                    @foreach(\App\Models\Batch::all() as $pl)
                        <option value="{{$pl->id}}" {{$pl->id == $year ? 'selected' : ''}}>{{$pl->name}}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-dark">{{__('text.word_get')}}</button>
            </div>
        </form>
        <table class="table adv-table">
            <thead class="text-capitalize bg-dark">
                <th>{{__('text.sn')}}</th>
                <th>{{__('text.word_background')}}</th>
                <th>{{__('text.word_semester')}}</th>
                <th>{{__('text.word_status')}}</th>
                <th>{{__('text.word_action')}}</th>
            </thead>
            <tbody id="courses">
                @foreach (\App\Models\Background::all() as $bg)
                    @foreach ($bg->semesters()->orderBy('sem')->get() as $sem)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$bg->background_name}}</td>
                            <td>{{$sem->name}}</td>
                            <td>@if (\App\Models\Result::where(['semester_id'=>$sem->id, 'published'=>1, 'batch_id'=>$year, 'campus_id'=>auth()->user()->campus_id])->count() == 0)
                                    <span class="text-primary">{{__('text.word_unpublished')}}</span>
                                @elseif (\App\Models\Result::where(['semester_id'=>$sem->id, 'published'=>0, 'batch_id'=>$year, 'campus_id'=>auth()->user()->campus_id])->count() == 0)
                                    <span class="text-success">{{__('text.word_published')}}</span>
                                @else
                                    <span class="text-info">{{__('text.word_mixed')}}</span>
                                @endif
                            </td>
                            <td>
                                @if (\App\Models\Result::where(['semester_id'=>$sem->id, 'published'=>1, 'batch_id'=>$year, 'campus_id'=>auth()->user()->campus_id])->count() == 0)
                                    <a class="btn btn-sm btn-primary" href="{{route('admin.result.publish', ['year'=>$year, 'semester'=>$sem->id])}}">{{__('text.word_publish')}}</a>
                                @elseif (\App\Models\Result::where(['semester_id'=>$sem->id, 'published'=>0, 'batch_id'=>$year, 'campus_id'=>auth()->user()->campus_id])->count() == 0)
                                    <a class="btn btn-sm btn-success" href="{{route('admin.result.unpublish', ['year'=>$year, 'semester'=>$sem->id])}}">{{__('text.word_unpublish')}}</a>
                                @else
                                    <a class="btn btn-sm btn-primary" href="{{route('admin.result.publish', ['year'=>$year, 'semester'=>$sem->id])}}">{{__('text.word_publish')}}</a>
                                    <a class="btn btn-sm btn-success" href="{{route('admin.result.unpublish', ['year'=>$year, 'semester'=>$sem->id])}}">{{__('text.word_unpublish')}}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('script')
<script>
    function formFilled(){
        $('#actions').removeClass('hidden');
    }

    function _import(_class, _subject) {
        event.preventDefault();
        url = "{{route('admin.result.ca.import', ['_C_', '_S_'])}}";
        url = url.replace('_C_', _class);
        url = url.replace('_S_', _subject);
        window.location = url;

    }

    function _fill(_class, _subject) {
        event.preventDefault();
        url = "{{route('admin.result.ca.fill', ['_C_', '_S_'])}}";
        url = url.replace('_C_', _class);
        url = url.replace('_S_', _subject);
        window.location = url;

    }

    function subjects() {
        _class = $('#_class_').val();
        url = "{{route('class_subjects', '_C_')}}";
        url = url.replace('_C_', _class);
        $.ajax({
            method: 'GET',
            url: url,
            success:function(data){
                console.log(data);
                html = ``;
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                    html += `<tr class="border-bottom border-dark">
                                <td class="border-right border-light">`+ (1+index) +`</td>
                                <td class="border-right border-light">`+element.code+`</td>
                                <td class="border-right border-light">`+element.name+`</td>
                                <td class="border-right border-light">`+element.semester+`</td>
                                <td class="border-right border-light">
                                    <button onclick="_fill(`+_class+`, `+element.id+`)" class="btn btn-sm btn-success"">{{__('text.word_fill')}}</button>|
                                    <button onclick="_import(`+_class+`, `+element.id+`)" class="btn btn-sm btn-primary">{{__('text.word_import')}}</button>
                                </td>
                            </tr>`;
                }
                // console.log(html);
                $('#courses').html(html);

            }
        })
    }
</script>
@endsection