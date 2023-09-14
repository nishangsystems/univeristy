@extends('admin.layout')

@section('section')

<div class="row m-4">
          <div class="col-lg-12">
                <form class="cmxform form-horizontal form m-4 py-4 style-form" method="post" action="{{route('faqs.update', [request('id')])}}">
                {{csrf_field()}}
                    <div class="form-group text-capitalize">
                        <label class="col-md-2" > {{__('text.word_question')}}</label>
                        <div class="col-md-9">
                            <input type="text" name="question" required  placeholder="Title" class="form-control" value="{{$item->question}}"/>
                        </div>
                    </div>
                    

                     <div class="form-group ">
                        <label for="description" class="control-label col-md-2 text-capitalize">{{__('text.word_answer')}}</label>
                        <div class="col-lg-9 p-4">
                        <textarea class="form-control w-100"  required name="answer" id="content">{{$item->answer}}</textarea>
                        </div>
                    </div>
                       
                
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-success btn-xs m-2" type="submit">{{__('text.word_save')}}</button>
                        <a href="{{route('faqs.index')}}" class="btn btn-danger btn-xs m-2" type="button">{{__('text.word_cancel')}}</a>
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