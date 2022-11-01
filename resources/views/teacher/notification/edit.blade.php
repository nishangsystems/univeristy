@extends('teacher.layout')

@section('section')
<div class="row m-4">
          <div class="col-lg-12">
                <form class="cmxform form-horizontal form m-4 py-4 style-form" method="post" action="{{route('admin.notification.update', $notification->id)}}">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="put">
                    <div class="form-group">
                        <label class="col-md-1" > Title</label>
                        <div class="col-md-10">
                            <input type="text" name="title" value="{{$notification->title}}" required  placeholder="Title" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group flex-wrap">
                        <div class="col-md-1"></div>
                        <div class="col-md-5">
                            <label>Program</label>
                            <div>
                                <select class="form-control" name="program" required data-placeholder="Select Program...">
                                    <option value="0"> For all Derpatment</option>
                                    @foreach(\App\Options::all() as $program)
                                        <option value="{{$program->id}}" {{($notification->program_id == $program->id)?'selected':''}}> {{$program->byLocale()->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label>Level</label>
                            <div>
                                <select class="form-control" name="level" required data-placeholder="Select Level...">
                                    <option value="0">  For all Level</option>
                                    @foreach(\App\Level::all() as $program)
                                        <option value="{{$program->id}}" {{($notification->level_id == $program->id)?'selected':''}}> {{$program->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                     </div>


                     <div class="form-group">
                        <label class="col-md-1"> Submission Date</label>
                        <div class="col-md-10">
                            <input type="date" required name="date"  value="{{$notification->date}}" placeholder="Submission Date" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-5">Make notification visible to teachers</label>
                        <div class="col-md-1">
                            <input  {{($notification->for_teachers == 1)?'checked':''}} type="checkbox" required name="teachers"  placeholder="Submission Date" class="form-control" />
                        </div>
                    </div>
                        
                    

                     <div class="form-group ">
                        <label for="description" class="control-label col-md-1">Description</label>
                        <div class="col-lg-10 p-4">
                        <textarea class="form-control"  required name="content" id="content">{{$notification->content}}</textarea>
                        </div>
                    </div>
                       
                
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-success btn-xs m-2" type="submit">Save</button>
                        <a href="{{route('admin.notification.index')}}" class="btn btn-danger btn-xs m-2" type="button">Cancel</a>
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