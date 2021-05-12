@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST">
                @csrf
                <h5 class="mt-5 mb-4 font-weight-bold">Select class and batch to generate matricule number</h5>
                <div class="form-group @error('section') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">Batch</label>
                    <div class="col-lg-10">
                        <div>
                            <select class="form-control" required name="batch">
                                <option  disabled>Select Batch</option>
                                @forelse(\App\Models\Batch::orderBy('name')->get() as $section)
                                    <option {{old('batch') == $section->id?'selected':''}} value="{{$section->id}}">{{$section->name}}</option>
                                @empty
                                    <option>No batch Created</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div id="section">
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2">Section</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" id="section0">
                                    <option selected disabled>Select Section</option>
                                    @forelse(\App\Models\SchoolUnits::where('parent_id',0)->get() as $section)
                                        <option value="{{$section->id}}">{{$section->name}}</option>
                                    @empty
                                        <option>No Sections Created</option>
                                    @endforelse
                                </select>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button id="save" class="btn btn-xs btn-primary mx-3" style="display: none" type="submit">Generate</button>
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
                      html += '<div class="mt-3"><select onchange="refresh($(this))" class="form-control section" name="'+data.name+'[]">';
                      html += '<option selected > Select ' + data.name + '</option>';
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
