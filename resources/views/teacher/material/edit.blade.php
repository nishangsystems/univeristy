@extends('teacher.layout')

@section('section')

<div class="row m-4">
          <div class="col-lg-12">
                <form class="cmxform form-horizontal form m-4 py-4 style-form" method="post" action="{{route('material.save', [request('layer'), request('layer_id'), request('campus_id')])}}">
                {{csrf_field()}}
                    <div class="form-group text-capitalize">
                        <label class="col-md-2" > {{__('text.word_title')}}</label>
                        <div class="col-md-9">
                            <input type="text" name="title" required  placeholder="Title" class="form-control" value="{{$item->title}}"/>
                        </div>
                    </div>

                    @if(request('layer') == 'S'||'F'||'D'||'P')
                        <input type="hidden" name="school_unit_id" value="{{request('layer_id')}}">
                    @endif
                    @if(request('layer') == 'S'||'F'||'D')
                        <div class="form-group text-capitalize">
                            <label class="col-md-2">{{__('text.word_level')}}</label>
                            <div class="col-md-9 text-capitalize">
                                <select class="form-control" name="level_id" data-placeholder="Select Level...">
                                    <option value="0"> {{__('text.for_all_levels')}}</option>
                                    @foreach(\App\Models\Level::all() as $program)
                                        <option value="{{$program->id}}" {{$item->level_id == $program->id ? 'selected' : null}}> {{$program->level}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    @if(request('layer') == 'L')
                        <input type="hidden" name="level_id" value="{{request('layer_id')}}">
                    @endif 
                    @if(request('layer') == 'C')
                        @php($pl = \App\Models\ProgramLevel::find(request('layer_id')))
                        <input type="hidden" name="school_unit_id" value="{{$pl->program_id}}">
                        <input type="hidden" name="level_id" value="{{$pl->level_id}}">
                    @endif 

                    <div class="form-group">
                        <label class="col-md-2 text-capitalize">{{__('text.word_visibility')}}</label>
                        <div class="col-md-9">
                            <select name="visibility" class="form-control" required id="">
                                <option value="">{{__('text.select_visibility')}}</option>
                                <option value="general" {{$item->visibility == 'general' ? 'selected' : ''}}>{{__('text.word_general')}}</option>
                                <option value="students" {{$item->visibility == 'students' ? 'selected' : ''}}>{{__('text.word_students')}}</option>
                                <option value="teachers" {{$item->visibility == 'teachers' ? 'selected' : ''}}>{{__('text.word_teachers')}}</option>
                            </select>
                        </div>
                    </div>

                    <?php /*<div class="form-group" id="file-box">
                        <label class="col-md-2 text-capitalize"> {{__('text.word_file')}}</label>
                        <div class="col-md-9">
                            <input name="file" type="file" class="form-control" required value=""/>
                        </div> 
                    </div> */ ?>

                    <div class="form-group " id="text-area">
                        <label for="description" class="control-label col-md-2 text-capitalize">{{__('text.word_description')}}</label>
                        <div class="col-lg-9 p-4">
                        <textarea class="form-control w-100" name="message" id="content">{{$item->message ?? ''}}</textarea>
                        </div>
                    </div>
                       
                
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-success btn-xs m-2" type="submit">{{__('text.word_save')}}</button>
                        <a href="{{route('notifications.index', [request('layer'), request('layer_id'), request('campus_id')])}}" class="btn btn-danger btn-xs m-2" type="button">{{__('text.word_cancel')}}</a>
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
        CKEDITOR.replace('content');
    </script>
@stop