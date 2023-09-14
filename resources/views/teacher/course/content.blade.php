@extends('teacher.layout')

@section('section')

<div class="row m-4">
    <div class="col-md-6 col-lg-6">
        @if($level == 2)
            <form class="form bg-light my-4 border rounded mx-2" method="get">
                <div class="py-4 input-group-merge d-flex">
                    <label class="py-2 fw-bold text-capitalize input-group-text">{{__('text.select_campus')}}</label>
                    <select class="form-control rounded" name="campus">
                        <option></option>
                        <@foreach (\App\Models\Campus::all() as $campus)
                            <option value="{{$campus->id}}" {{request('campus') == $campus->id ? 'selected' : ''}}>{{$campus->name}}</option>
                        @endforeach
                    </select>
                    <input class="btn btn-sm btn-primary" type="submit" value="{{__('text.word_get')}}">
                </div>
            </form>
        @endif
        @if ($level == 1 || ($level == 2 && request()->has('campus')) && (request('campus') != null))
            <form class="bg-light rounded form my-4 mx-2 py-4 px-3" method="post" action="{{route('user.subject.topics', ['subject_id'=>$subject_id, 'parent_id'=>$parent_id, 'level'=>$level])}}">
                {{csrf_field()}}
                <div class="p-4">
                    @if ($level == 1)
                        <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_topic')}}</label>
                        <textarea class="form-control rounded w-100" rows="3"  required name="title" id="content"></textarea>
                    @endif
                    @if ($level == 2)
                        @if ((request()->has('campus')) && (request('campus') != null))
                            <div class="text-right h5 text-uppercase">{{\App\Models\Campus::find(request('campus'))->name??null.' '.__('text.word_campus')}}</div>
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.sub_topic')}}</label>
                            <textarea class="form-control rounded w-100" rows="3"  required name="title" id="content"></textarea>
                            <input type="hidden" name="teacher_id" value="{{auth()->id()}}">
                            <input type="hidden" name="campus_id" value="{{request('campus')}}">
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_duration').'('.__('text.in_hours').')'}}</label>
                            <input class="form-control rounded" type="number" min="0" required name="duration">
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_week')}}</label>
                            <input class="form-control rounded" type="number" min="0" required name="week">
                        @endif
                    @endif
                    <div class="d-flex justify-content-end py-3">
                        <input class="btn btn-sm btn-primary" type="submit" value="{{__('text.word_save')}}">
                    </div>
                </div>
            </form>  
        @endif
    </div>
    <div class="col-md-6 col-lg-6">
        <table class="adv-table table">
            <thead class="text-capitalize">
                <th>{{__('text.sn')}}</th>
                @if($level == 2)
                    <th>{{__('text.sub_topic')}}</th>
                    <th>{{__('text.word_duration')}}</th>
                    <th>{{__('text.word_week')}}</th>
                    <th></th>
                @endif
                @if ($level == 1)
                    <th>{{__('text.word_topic')}}</th>
                    <th></th>
                @endif
            </thead>
            <tbody>
                @php($k = 1)
                @foreach ($content as $item)
                    <tr>
                        <td>{{$k++}}</td>
                        <td>{!! $item->title !!}</td>
                        @if ($level == 2)
                            <td>{{$item->duration}}</td>
                            <td>{{$item->week}}</td>
                        @endif
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{route('user.subject.content.edit', ['topic_id'=>$item->id, 'subject_id'=>$item->subject_id])}}?campus={{request('campus')}}">{{__('text.word_edit')}}</a>
                            @if($level == 1)
                                <a class="btn btn-sm btn-primary" href="{{route('user.subject.content', ['subject_id'=>$item->subject_id, 'level'=>2, 'parent_id'=>$item->id])}}">{{__('text.sub_topics')}}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <!-- /col-lg-12 -->
    </div>
    <!-- /row -->
@stop

@section('script')
{{-- <script>
var editor1 = new RichTextEditor("#content");
</script> --}}
    <script src="{{ asset('assets/js') }}/ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('objective');
        CKEDITOR.replace('outcomes');
    </script>
@stop