@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST">
                @csrf
                <h5 class="mt-5 mb-4 font-weight-bold">{{__('text.phrase_9')}}</h5>
                <div class="form-group @error('section') has-error @enderror text-capitalize">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_batch')}}</label>
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
                <div id="section">
                    <div class="form-group text-capitalize">
                        <label for="cname" class="control-label col-lg-2">{{__('text.word_section')}}</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" id="section0" name="section">
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
                        <button id="save" class="btn btn-xs btn-primary mx-3" style="display: none" type="submit">{{__('text.word_generate')}}</button>
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
              success: function (response) {
                  $(".pre-loader").css("display", "none");
                  let html = "";
                  if(response.data.length > 0){
                      $('#save').css("display", "block");
                  }else{
                      $('#save').css("display", "none");
                  }

                  if (response.data.length > 0) {
                    html += '<div class="mt-3"><select class="form-control section" name="section">';
                    for (i = 0; i < response.data.length; i++) {
                        if(response.data[i].sub_units.length == 0){
                            html += '<option value="' + response.data[i].id + '">' + response.data[i].name + '</option>';
                        }
                        for (j = 0; j < response.data[i].sub_units.length; j ++){
                           html += '<option value="' + response.data[i].sub_units[j].id + '">' + response.data[i].name +"     :    " + response.data[i].sub_units[j].name + '</option>';
                        }
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
