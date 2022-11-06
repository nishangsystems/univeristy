@extends('teacher.layout')

@section('section')

<div class="row m-4">
          <div class="col-lg-12">
                <form class="cmxform form-horizontal form m-4 py-4 style-form" method="post" action="{{route('material.save')}}" enctype="multipart/form-data">
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

                    @if((request('program_level_id') != null))
                        @php($pl = \App\Models\ProgramLevel::find(request('program_level_id')))
                        <input type="hidden" name="school_unit_id" value="{{$pl->program_id}}">
                        <input type="hidden" name="level_id" value="{{$pl->level_id}}">
                    @else 
                        @if(request('type') == 'departmental')
                            <input type="hidden" name="school_unit_id" value="{{auth()->user()->classes()->first()->id}}">
                        @else
                            <div class="form-group flex-wrap">
                                <div class="col-md-2"></div>
                                <div class="col-md-5 text-capitalize">
                                    <label>{{__('text.word_program')}}</label>
                                    <div>
                                        <select class="form-control" name="school_unit_id" data-placeholder="Select Program...">
                                            <option value="0"> {{__('text.for_all_programs')}}</option>
                                            @foreach(\App\Models\SchoolUnits::whereIn('unit_id', [4])->where('parent_id', auth()->user()->classes()->first()->id)->orderBy('name')->get() as $program)
                                                @if($program->unit_id == 4)
                                                    <option value="{{$program->id}}"> {{$program->name}}</option>
                                                @else
                                                    <option value="{{$program->id}}"> {{$program->name.' ('.$program->parent()->first()->name.')'}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 text-capitalize">
                                    <label>{{__('text.word_level')}}</label>
                                    <div>
                                        <select class="form-control" name="level_id" data-placeholder="Select Level...">
                                            <option value="0"> {{__('text.for_all_levels')}}</option>
                                            @foreach(\App\Models\Level::all() as $program)
                                                <option value="{{$program->id}}"> {{$program->level}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

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

                    <div class="form-group" id="file-box">
                        <label class="col-md-2 text-capitalize"> {{__('text.word_file')}}</label>
                        <div class="col-md-9">
                            <input name="file" type="file" class="form-control" required />
                        </div>
                    </div>

                    <div class="form-group " id="text-area">
                        <label for="description" class="control-label col-md-2 text-capitalize">{{__('text.word_description')}}</label>
                        <div class="col-lg-9 p-4">
                        <textarea class="form-control" name="message" id="content"></textarea>
                        </div>
                    </div>
                       
                
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-success btn-xs m-2" type="submit">{{__('text.word_save')}}</button>
                        <a href="{{route('material.index')}}" class="btn btn-danger btn-xs m-2" type="button">{{__('text.word_cancel')}}</a>
                        </div>
                    </div>
                </form>
          </div>
          <!-- /col-lg-12 -->
        </div>
        <!-- /row -->
@stop

@section('script')
<script src="{{ asset('public/assets/js') }}/ckeditor/ckeditor.js"></script>
<script>
CKEDITOR.replace('content');
</script>
@stop