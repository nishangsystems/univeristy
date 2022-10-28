@extends('admin.layout')

@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" action="{{route('admin.users.classmaster')}}" method="POST">

                <input name="type" value="{{request('type','teacher')}}" type="hidden"/>
                <h5 class="mt-5 font-weight-bold">Teachers Info</h5>
                @csrf

                <div class="form-group @error('gender') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">Teacher</label>
                    <div class="col-lg-10">
                        <select required  class="form-control" name="user_id">
                            <option selected disabled>Select Teacher</option>
                          @foreach(\App\Models\User::where('type','teacher')->get() as $user)
                                <option {{old('user_id') == $user->id?'selected':''}} value="{{$user->id}}">{{$user->name}}</option>
                          @endforeach
                        </select>
                        @error('gender')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h5 class="mt-5 mb-4 font-weight-bold">Class Information</h5>

                <div id="section">
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2">Section</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" name="section" id="section0">
                                    <option selected disabled>Select Section</option>
                                    @forelse($classes as $id => $section)
                                        <option value="{{$id}}">{{$section}}</option>
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
                        <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.section').on('change', function () {
            // refresh($(this));
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
