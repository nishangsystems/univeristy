@extends('teacher.layout')

@section('section')

<div class="row m-4">
          <div class="col-lg-12">
                <form class="cmxform form-horizontal form m-4 py-4 style-form" method="post" action="{{route('notifications.save', [request('layer'), request('layer_id'), request('campus_id')])}}">
                {{csrf_field()}}
                    <input type="hidden" name="type" id="" value="{{request('type') ?? ''}}">
                    <input type="hidden" name="program_level_id" id="" value="{{request('program_level_id') ?? ''}}">
                    <input type="hidden" name="campus_id" id="" value="{{request('campus_id') ?? ''}}">
                    <input type="hidden" name="_d" id="" value="{{request('_d') ?? ''}}">
                    <div class="form-group text-capitalize">
                        <label class="col-md-2" > {{__('text.word_title')}}</label>
                        <div class="col-md-9">
                            <input type="text" name="title" required  placeholder="Title" class="form-control" />
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
                                    <option value="0"> {{__('text.word_all')}}</option>
                                    @foreach(\App\Models\Level::all() as $program)
                                        <option value="{{$program->id}}"> {{$program->level}}</option>
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
                        <label class="col-md-2 text-capitalize"> {{__('text.submission_date')}}</label>
                        <div class="col-md-9">
                            <input type="date" required name="date"  placeholder="Submission Date" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 text-capitalize">{{__('text.word_visibility')}}</label>
                        <div class="col-md-9">
                            <select name="visibility" class="form-control" required id="">
                                <option value="">{{__('text.select_visibility')}}</option>
                                <option value="general">{{__('text.word_general')}}</option>
                                <option value="students">{{__('text.word_students')}}</option>
                                <option value="teachers">{{__('text.word_teachers')}}</option>
                            </select>
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