@extends('admin.layout')

@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" action="{{route('admin.users.classmaster')}}" method="POST">

                <input name="type" value="{{request('type','teacher')}}" type="hidden"/>
                <h5 class="mt-5 font-weight-bold">Teachers Info</h5>
                @csrf

                <div class="form-group @error('user_id') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{trans_choice('text.word_teacher', 1)}}</label>
                    <div class="col-lg-10">
                        <select required  class="form-control" name="user_id">
                            <option selected disabled>{{__('text.select_teacher')}}</option>
                          @foreach(\App\Models\User::where('type','teacher')->get() as $user)
                                <option {{old('user_id') == $user->id?'selected':''}} value="{{$user->id}}">{{$user->name}}</option>
                          @endforeach
                        </select>
                        @error('user_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <h5 class="mt-5 mb-4 font-weight-bold">Class Information</h5>
                
                <div class="form-group @error('campus_id') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_campus')}}</label>
                    <div class="col-lg-10">
                        <select required  class="form-control" name="campus_id">
                            <option selected disabled>{{__('text.select_campus')}}</option>
                            @foreach(\App\Models\Campus::all() as $campus)
                                <option {{old('campus_id') == $campus->id?'selected':''}} value="{{$campus->id}}">{{$campus->name}}</option>
                            @endforeach
                        </select>
                        @error('campus_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div id="section">
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2">Section</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" name="section" id="section0">
                                    <option selected disabled>Select Section</option>
                                    @forelse(\App\Models\SchoolUnits::where(['unit_id'=>3])->get() as $id => $section)
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
