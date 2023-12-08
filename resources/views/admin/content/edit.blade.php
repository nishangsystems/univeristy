@extends('admin.layout')

@section('section')

<div class="row m-4">
    <div class="col-md-6 col-lg-6">
        
        <form class="bg-light rounded  my-4 mx-2 py-4 px-3" method="post" >
            {{csrf_field()}}
            <div class="p-4">
                <label class="py-2 fw-bold text-capitalize">{{__('text.select_campus')}}</label>
                <select class="form-control rounded" name="campus_id">
                    <option></option>
                    @foreach (\App\Models\Campus::all() as $campus)
                        <option value="{{$campus->id}}" {{$campus_id == $campus->id ? 'selected' : ''}}>{{$campus->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-4">
                @if ($level == 1)
                    <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_topic')}}</label>
                    <textarea class="form-control rounded w-100" rows="3"  required name="title" id="content">{!! old('title', $topic->title) !!}</textarea>
                @endif
                @if ($level == 2)
                    <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.sub_topic')}}</label>
                    <textarea class="form-control rounded w-100" rows="3"  required name="title" id="content">{!! old('title', $topic->title) !!}</textarea>
                    <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_duration').'('.__('text.in_hours').')'}}</label>
                    <input class="form-control rounded" type="number" min="0" required name="duration" value="{{ old('duration', $topic->duration) }}">
                    <label for="topic" class="py-2 fw-bold text-capitalize">{{__('text.word_week')}}</label>
                    <input class="form-control rounded" type="number" min="0" required name="week" value="{{ old('week', $topic->week) }}">
                @endif
                <div class="d-flex justify-content-end py-3">
                    <input class="btn btn-sm btn-primary" type="submit" value="{{__('text.word_save')}}">
                </div>
            </div>
        </form>  
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
                            <a class="btn btn-sm btn-primary" href="{{route('admin.users.content.edit', [$teacher_id, $subject_id, $item->id])}}">{{__('text.word_edit')}}</a>
                            @if($level == 1)
                                <a class="btn btn-sm btn-success" href="{{route('admin.users.content.index', [$teacher_id, $subject_id, $item->id])}}">{{__('text.sub_topics')}}</a>
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
    {{-- <script src="{{ asset('assets/js') }}/ckeditor/ckeditor.js"></script>
    <script>
        // CKEDITOR.replace('objective');
        CKEDITOR.replace('content');
    </script> --}}
@stop