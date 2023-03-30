@extends('teacher.layout')

@section('section')

<div class="row m-4">
          <div class="col-md-5">
                <form class="bg-light rounded form-horizontal form m-4 py-4 px-3" method="post">
                {{csrf_field()}}
                        
                    <div class="p-4">
                        @if ($level == 1)
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_topic')}}</label>
                            <textarea class="form-control rounded" rows="3"  required name="title">{{$topic->title}}</textarea>
                        @endif
                        @if ($level == 2)
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.sub_topic')}}</label>
                            <textarea class="form-control rounded" rows="3"  required name="title">{{$topic->title}}</textarea>
                            <input type="hidden" name="teacher_id" value="{{auth()->id()}}">
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_duration').'('.__('text.in_hours').')'}}</label>
                            <input class="form-control rounded" type="number" min="0" required name="duration" value="{{$topic->duration}}">
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_week')}}</label>
                            <input class="form-control rounded" type="number" min="0" required name="week" value="{{$topic->week}}">
                        @endif
                        <div class="d-flex justify-content-end py-3">
                            <input class="btn btn-sm btn-primary" type="submit" value="{{__('text.word_save')}}">
                        </div>
                    </div>
                       
 
                </form>
          </div>
          <div class="col-md-7">
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
                            <td>{{$item->title}}</td>
                            @if ($level == 2)
                                <td>{{$item->duration}}</td>
                                <td>{{$item->week}}</td>
                            @endif
                            <td>
                                <a class="btn btn-sm btn-primary" href="{{route('user.subject.content.edit', ['topic_id'=>$item->id, 'subject_id'=>$item->subject_id])}}">{{__('text.word_edit')}}</a>
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
<script src="{{ asset('public/assets/js') }}/ckeditor/ckeditor.js"></script>
<script>
// CKEDITOR.replace('content');
</script>
@stop