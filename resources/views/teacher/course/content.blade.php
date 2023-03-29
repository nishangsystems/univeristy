@extends('teacher.layout')

@section('section')

<div class="row m-4">
          <div class="col-md-5">
                <form class="bg-light rounded form-horizontal form m-4 py-4 px-3" method="post" action="{{route('user.subject.topics', ['subject_id'=>$subject_id, 'parent_id'=>$parent_id, 'level'=>$level])}}">
                {{csrf_field()}}
                        
                    <div class="p-4">
                        <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_topic')}}</label>
                        <textarea class="form-control rounded" rows="3"  required name="title" id="content"></textarea>
                        @if ($level == 1)
                            <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_duration').'('.__('text.in_hours').')'}}</label>
                            <input class="form-control rounded" type="number"  required name="coverage_duration" id="content">
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
                    <th>{{__('text.word_topic')}}</th>
                    @if ($level == 1)
                        <th>{{__('text.word_duration')}}</th>
                        <th></th>
                    @endif
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach ($content as $item)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$item->title}}</td>
                            @if ($level == 1)
                                <td>{{$item->coverage_duration}}</td>
                                <td>
                                    <a class="btn btn-sm btn-primary" href="{{route('user.subject.content', ['subject_id'=>$item->subject_id, 'level'=>2, 'parent_id'=>$item->id])}}">{{__('text.word_topics')}}</a>
                                </td>
                            @endif
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