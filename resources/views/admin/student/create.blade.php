@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.student.store')}}">

            <input name="type" value="{{request('type','teacher')}}" type="hidden" />
            <h5 class="mt-5 font-weight-bold text-capitalize">{{_('text.personal_information')}}</h5>
            @csrf
            <div class="form-group @error('name') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.full_name')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="name" type="text" required />
                    @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('matric') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_matricule')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="matric" value="{{old('matric')}}" type="text"  />
                    @error('matric')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('email') has-error @enderror">
                <label for="email" class="control-label col-lg-2 text-capitalize">{{__('text.word_email')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="email" value="{{old('email')}}" type="email"  />
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('phone') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_phone')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="phone" value="{{old('phone')}}" type="tel"  />
                    @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('address') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_address')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="address" value="{{old('address')}}" type="text"  />
                    @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('dob') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.date_of_birth')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="dob" value="{{old('dob')}}" type="date"  />
                    @error('dob')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('gender') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_gender')}} </label>
                <div class="col-lg-10">
                    <select class=" form-control" name="gender" >
                        <option value="">{{__('text.word_gender')}}</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            
            <h5 class="mt-5 mb-4 font-weight-bold text-capitalize">{{__('text.admission_class_information')}}</h5>
            
            <div class="form-group @error('year') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.academic_year')}}</label>
                <div class="col-lg-10">
                    <select class=" form-control" name="year" >
                        <option value="">select academic year</option>
                        @forelse(\App\Models\Batch::all() as $batch)
                            <option value="{{$batch->id}}">{{$batch->name}}</option>
                        @empty
                            <option value="" selected>No data found</option>
                        @endforelse
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('program_id') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_program')}}</label>
                <div class="col-lg-10">
                    <select class=" form-control" name="program_id" >
                        <option value="">{{__('text.select_program')}}</option>
                        @forelse(\App\Http\Controllers\Admin\StudentController::getMainClasses() as $k=>$v)
                            <option value="{{$k}}">{{$v}}</option>
                        @empty
                            <option value="" selected>No data found</option>
                        @endforelse
                    </select>
                    @error('program_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-group @error('campus_id') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_campus')}} </label>
                <div class="col-lg-10">
                    <select name="campus_id" class="form-control" id="">
                        <option value="">select campus</option>
                        @forelse(\App\Models\Campus::all() as $campus)
                            <option value="{{$campus->id}}">{{$campus->name}}</option>
                        @empty
                            <option value="" selected>No data found</option>
                        @endforelse
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div id="section">
                <div class="form-group text-capitalize">
                    <label for="cname" class="control-label col-lg-2"> Class <span style="color:red">*</span></label>
                    <div class="col-lg-10">
                        <div>
                            <select name="section" class="form-control section" id="section0" required>
                                <option selected disabled>{{__('text.select_class')}}</option>
                                @if(isset($options))
                                    @foreach($options as $key => $option)
                                        <option value="{{$key}}">{{$option}}</option>
                                    @endforeach
                                @else
                                    <option>{{__('text.no_sections_created')}}</option>
                                @endif
                            </select>
                            <div class="children"></div>
                        </div>
                    </div>
                </div>
            </div>
            
           
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3" style="display: block" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger" href="{{route('admin.users.index')}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('script')
<!-- <script>
    $('.section').on('change', function() {
        refresh($(this));
    })

    function refresh(div) {
        $(".pre-loader").css("display", "block");
        url = "{{route('section-children', "+
        VALUE +")}}";
        url = url.replace('VALUE', div.val());
        $.ajax({
            type: "GET",
            url: url,
            success: function(data) {
                $(".pre-loader").css("display", "none");
                let html = "";
                if (data.valid == 1) {
                    $('#save').css("display", "block");
                } else {
                    $('#save').css("display", "none");
                }
                if (data.array.length > 0) {
                    html += '<div class="mt-3"><select onchange="refresh($(this))" class="form-control section" name="' + data.name + '">';
                    html += '<option selected > Select ' + data.name + '</option>';
                    for (i = 0; i < data.array.length; i++) {
                        html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
                    }
                    html += '</select>' +
                        '<div class="children"></div></div>';
                }
                div.parent().find('.children').html(html)
            },
            error: function(e) {
                $(".pre-loader").css("display", "none");
            }
        });
    }
</script> -->
@endsection
