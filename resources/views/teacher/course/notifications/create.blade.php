@extends('teacher.layout')

@section('section')

<div class="row m-4">
          <div class="col-lg-12">
                <form class="cmxform form-horizontal form m-4 py-4 style-form" method="post" action="{{route('course.notification.save', request('course_id'))}}">
                {{csrf_field()}}
                    <input type="hidden" name="type" id="" value="{{request('type') ?? ''}}">
                    <input type="hidden" name="program_level_id" id="" value="{{request('program_level_id') ?? ''}}">
                    <input type="hidden" name="campus_id" id="" value="{{request('campus_id') ?? ''}}">
                    <div class="form-group text-capitalize">
                        <label class="col-md-2" > {{__('text.word_title')}}</label>
                        <div class="col-md-9">
                            <input type="text" name="title" required  placeholder="Title" class="form-control" />
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <div class="col-md-2"><label>{{__('text.word_campus')}}</label></div>
                        <div class="col-md-9 text-capitalize">
                            <select class="form-control" name="campus_id" data-placeholder="Select Program...">
                                <option value="0"> {{__('text.word_all')}}</option>
                                @foreach(\App\Models\Campus::all() as $campus)
                                    <option value="{{$campus->id}}"> {{$campus->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                        


                    <div class="form-group">
                        <label class="col-md-2 text-capitalize"> {{__('text.submission_date')}}</label>
                        <div class="col-md-9">
                            <input type="date" required name="date"  placeholder="Submission Date" class="form-control" />
                        </div>
                    </div>
                        
                    <div class="form-group ">
                        <label for="description" class="control-label col-md-2 text-capitalize">{{__('text.word_description')}}</label>
                        <div class="col-lg-9 p-4">
                        <textarea class="form-control w-100"  required name="message" id="content"></textarea>
                        </div>
                    </div>
                       
                
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-success btn-xs m-2" type="submit">{{__('text.word_save')}}</button>
                        <a href="{{route('course.notification.index', request('course_id'))}}" class="btn btn-danger btn-xs m-2" type="button">{{__('text.word_cancel')}}</a>
                        </div>
                    </div>
                </form>
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
        //CKEDITOR.replace('objective');
        CKEDITOR.replace('content');
    </script>

@stop