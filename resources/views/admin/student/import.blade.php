@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{route('admin.students.import')}}">
                @csrf
                <div class="form-group @error('section') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_batch')}}</label>
                    <div class="col-lg-10">
                        <div>
                            <select class="form-control" required name="batch">
                                <option  disabled>{{__('text.select_year')}}</option>
                                @forelse(\App\Models\Batch::orderBy('name')->get() as $section)
                                    <option {{old('batch') == $section->id?'selected':''}} value="{{$section->id}}">{{$section->name}}</option>
                                @empty
                                    <option>{{__('text.no_batch_created')}}</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group @error('file') has-error @enderror text-capitalize">
                    <label for="cname" class="control-label col-lg-2">{{__('text.excel_file')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="file" value="{{old('file')}}" type="file" required/>
                        @error('file')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h5 class="mt-5 mb-4 font-weight-bold text-capitalize">{{__('text.admission_class_information')}}</h5>

                <div id="section">
                    <div class="form-group text-capitalize">
                        <label for="cname" class="control-label col-lg-2">{{__('text.word_section')}}</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" id="section0">
                                    <option selected disabled>{{__('text.select_section')}}</option>
                                    @forelse(\App\Models\SchoolUnits::where('parent_id',0)->get() as $section)
                                        <option value="{{$section->id}}">{{$section->name}}</option>
                                    @empty
                                        <option>{{__('text.no_sections_created')}}</option>
                                    @endforelse
                                </select>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button id="save" class="btn btn-xs btn-primary mx-3" style="display: none" type="submit">{{_('text.word_save')}}</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.section').on('change', function () {
            refresh($(this));
        })

      function refresh(div) {
          $(".pre-loader").css("display", "block");
          url = "{{route('section-children', "VALUE")}}";
          url = url.replace('VALUE', div.val());
          $.ajax({
              type: "GET",
              url: url,
              success: function (data) {
                  $(".pre-loader").css("display", "none");
                  let html = "";
                  if(data.valid == 1){
                      $('#save').css("display", "block");
                  }else{
                      $('#save').css("display", "none");
                  }
                  if (data.array.length > 0) {
                      html += '<div class="mt-3"><select onchange="refresh($(this))" class="form-control section" name="'+data.name+'">';
                      html += '<option selected > {{__("text.word_select")}} ' + data.name + '</option>';
                      for (i = 0; i < data.array.length; i++) {
                          html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
                      }
                      html += '</select>' +
                          '<div class="children"></div></div>';
                  }
                  div.parent().find('.children').html(html)
              }, error: function (e) {
                  $(".pre-loader").css("display", "none");
              }
          });
      }
    </script>
@endsection
