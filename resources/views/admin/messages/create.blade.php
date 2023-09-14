@extends('admin.layout')

@section('section')

<div class="row m-4">
          <div class="col-lg-12">
                <form class="cmxform form-horizontal form m-4 py-4 style-form" method="post">
                {{csrf_field()}}
                    
                    <div class="form-group text-capitalize">
                        <label class="col-md-2" > {{__('text.word_title')}}</label>
                        <div class="col-md-9">
                            <input type="text" name="title" required  placeholder="Title" class="form-control" />
                        </div>
                    </div>
                    
                    <div class="form-group text-capitalize">
                        <label class="col-md-2">{{__('text.word_year')}}</label>
                        <div class="col-md-9 text-capitalize">
                            <select class="form-control" name="year_id" data-placeholder="Select Level...">
                                <option value=""> ------ </option>
                                @foreach(\App\Models\Batch::all() as $btch)
                                    <option value="{{$btch->id}}"> {{$btch->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                                            
                    
                    <div class="form-group text-capitalize">
                        <label class="col-md-2">{{__('text.unit_type')}}</label>
                        <div class="col-md-9 text-capitalize">
                            <select class="form-control text-uppercase" id="unit_type" name="unit_type" data-placeholder="Select Level..." oninput="loadUnits()">
                                <option > ------ </option>
                                <option value="1"> {{__('text.word_school')}}</option>
                                <option value="2"> {{__('text.word_faculty')}}</option>
                                <option value="3"> {{__('text.word_department')}}</option>
                                <option value="4"> {{__('text.word_program')}}</option>
                                <option value="5"> {{__('text.word_class')}}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group text-capitalize">
                        <label class="col-md-2">{{__('text.word_unit')}}</label>
                        <div class="col-md-9 text-capitalize">
                            <select class="form-control" name="unit_id" id="unit_id" data-placeholder="Select Level...">
                                <option value=""> ------ </option>
                                @foreach(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                                    <option value="{{$pl['id']}}"> {{$pl['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 text-capitalize">{{__('text.word_recipients')}}</label>
                        <div class="col-md-9">
                            <select name="recipients" class="form-control" required id="">
                                <option value="">{{__('text.select_recipients')}}</option>
                                <option value="students">{{__('text.word_students')}}</option>
                                <option value="teachers">{{__('text.word_teachers')}}</option>
                                <option value="parents">{{__('text.word_parents')}}</option>
                            </select>
                        </div>
                    </div>

                     <div class="form-group ">
                        <label for="description" class="control-label col-md-2 text-capitalize">{{__('text.word_message')}}</label>
                        <div class="col-lg-9 p-4">
                        <textarea class="form-control w-100"  required name="message" id="content"></textarea>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-success btn-xs m-2" type="submit">{{__('text.word_save')}}</button>
                        <a href="{{route('messages.sent', [request('layer'), request('layer_id'), request('campus_id')])}}" class="btn btn-danger btn-xs m-2" type="button">{{__('text.word_cancel')}}</a>
                        </div>
                    </div>
                </form>
          </div>
          <!-- /col-lg-12 -->
        </div>
        <!-- /row -->
@stop

@section('script')
<script src="{{ asset('assets/js') }}/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('content');

    // var editor1 = new RichTextEditor("#content");

    function loadUnits() {
        let unit_type = parseInt($('#unit_type').val());
        switch (unit_type) {
            case 5: // load classes
                url ="{{route('program_levels')}}";
                $.ajax({
                    method: 'get',
                    url: url,
                    success: function(data){
                        content = ``;
                        console.log(typeof data);
                        for (const key in object) {
                            if (Object.hasOwnProperty.call(object, key)) {
                                const element = object[key];
                                
                            }
                        } (let index = 0; index < data.length; index++) {
                            const element = data[index];
                            content += `<opiotn${element.id}>${element.name}</option>`;
                        }
                        $('#unit_id').html(content);
                    }
                })
                break;
        
            default:
                break;
        }
    }
</script>
@stop