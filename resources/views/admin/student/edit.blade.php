@extends('admin.layout')

@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.student.update',$student->id)}}">
                <input name="type" value="{{request('type','teacher')}}" type="hidden"/>
                <input name="_method" value="put" type="hidden"/>
                <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.personal_information')}}</h5>
                @csrf
                <div class="form-group @error('name') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.full_name')}} ({{__text.word_required}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="name" value="{{old('name', $student->name)}}" type="text" required/>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="form-group @error('email') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_email')}}  </label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="email" value="{{old('email', $student->email)}}" type="text"  />
                        @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('phone') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_phone')}}</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="phone" value="{{old('phone', $student->phone)}}" type="text"  />
                        @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('address') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_address')}}</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="address" value="{{old('address', $student->address)}}" type="text"  />
                        @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('religion') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{text.word_denomination}}</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="religion" value="{{old('religion', $student->religion)}}" type="text"  />
                        @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('gender') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_gender')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control text-capitalize" name="gender">
                            <option selected disabled>{{__('text.select_gender')}}</option>
                            <option {{old('gender', $student->gender) == 'male'?'selected':''}} value="male">{{__('text.word_male')}}</option>
                            <option {{old('gender', $student->gender) == 'female'?'selected':''}} value="female">{{__('text.word_female')}}</option>
                        </select>
                        @error('gender')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <h5 class="mt-5 mb-4 font-weight-bold text-capitalize">{{'text.admission_class_information'}}</h5>
                <div class="form-group @error('type') has-error @enderror text-capitalize">
                <label for="cname" class="control-label col-lg-2">{{__('text.school_section')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="type">
                            <option selected disabled>{{__('text.word_section')}}</option>
                            <option {{old('type', $student->type) == 'day'?'selected':''}} value="day">{{__('text.day_section')}}</option>
                            <option {{old('type', $student -> type) == 'boarding'?'selected':''}} value="boarding">{{__('text.boarding_section')}}</option>
                        </select>
                        @error('type')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div id="section">
                    <div class="form-group text-capitalize">
                        <label for="cname" class="control-label col-lg-2">{{__('text.word_section')}}</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" name="section" id="section0">
                                    <option selected disabled>{{__('text.word_section')}}</option>
                                    @foreach($classes as $key=>$section)
                                        <option value="{{$key}}" {{\App\Models\StudentClass::where('student_id', $student->id)->first()->class_id == $key ? "selected" : ""}}>{{$section}}</option>
                                    @endforeach
                                    
                                </select>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button  class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.users.index')}}" type="button">{{__('text.word_cancel')}}</a>
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
