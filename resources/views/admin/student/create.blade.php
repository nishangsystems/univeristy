@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.student.store')}}">

            <input name="type" value="{{request('type','teacher')}}" type="hidden" />
            <h5 class="mt-5 font-weight-bold">Personal Information</h5>
            @csrf
            <div class="form-group @error('name') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Full Name <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
                    @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <div class="form-group @error('email') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Email</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="email" value="{{old('email')}}" type="text" />
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('phone') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Phone </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="phone" value="{{old('phone')}}" type="text"  />
                    @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('address') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Address</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="address" value="{{old('address')}}" type="text" />
                    @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('address') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Denomination</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="religion" value="{{old('religion')}}" type="text" />
                    @error('religion')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('dob') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Date of Birth</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="dob" value="{{old('dob')}}" type="date" />
                    @error('dob')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('pob') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Place of Birth</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="pob" value="{{old('pob')}}" type="text" />
                    @error('pob')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('parent_name') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Parent's Name</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="parent_name" value="{{old('parent_name')}}" type="text" />
                    @error('parent_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('parent_phone_number') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Parent's or Guardian Phone Number </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="parent_phone_number" value="{{old('parent_phone_number')}}" type="text" />
                    @error('parent_phone_number')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('gender') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Gender</label>
                <div class="col-lg-10">
                    <select class="form-control" name="gender">
                        <option selected disabled>Select Gender</option>
                        <option {{old('gender') == 'male'?'selected':''}} value="male">Male</option>
                        <option {{old('gender') == 'female'?'selected':''}} value="female">Female</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <h5 class="mt-5 mb-4 font-weight-bold">Admission Class Information</h5>


            <div class="form-group @error('type') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">School Section <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="type">
                        <option selected disabled>Select</option>
                        <option {{old('type') == 'day'?'selected':''}} value="day">Day Section</option>
                        <option {{old('type') == 'boarding'?'selected':''}} value="boarding">Boarding Section</option>
                    </select>
                    @error('type')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div id="section">
                <div class="form-group">
                    <label for="cname" class="control-label col-lg-2">Section <span style="color:red">*</span></label>
                    <div class="col-lg-10">
                        <div>
                            <select name="section" class="form-control section" id="section0">
                                <option selected disabled>Select Section</option>
                                @if(isset($options))
                                    @foreach($options as $key => $option)
                                        <option value="{{$key}}">{{$option}}</option>
                                    @endforeach
                                @else
                                    <option>No Sections Created</option>
                                @endif
                            </select>
                            <div class="children"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3" style="display: block" type="submit">Save</button>
                    <a class="btn btn-xs btn-danger" href="{{route('admin.users.index')}}" type="button">Cancel</a>
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