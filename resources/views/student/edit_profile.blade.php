@extends('student.layout')
@section('section')
 <div class="col-md-10 mx-auto py-4">
    <form action="{{route('student.update_profile')}}" method="post">
        @csrf
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_phone')}}</label>
            <div class="col-md-9">
                <input type="number" name="phone" id="" class="form-control border rounded" value="{{auth('student')->user()->phone ?? ''}}">
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.parents_phone_number')}}</label>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control border rounded" id="country_picker" name="parent_phone_code" onchange="set_field_length(this)">
                            @foreach (collect(config('country-phone-data'))->sortBy('label') as $phdata)
                                <option value="{{ $phdata['phone'] }}" data-length="{{ json_encode($phdata['phoneLength']) }}" {{ old('parent_phone_code', auth('student')->user()->parent_phone_code) == $phdata['phone'] ? 'selected' : '' }}>{{ $phdata['phone'].' - '.$phdata['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="parent_phone_number" minlength="0" maxlength="0" placeholder="phone number here without country code" id="parent_phone" class="form-control border rounded" value="{{ auth('student')->user()->parent_phone_number ?? '' }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_email')}}</label>
            <div class="col-md-9">
                <input type="email" name="email" id="" class="form-control border rounded" value="{{auth('student')->user()->email}}">
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_gender')}}</label>
            <div class="col-md-9">
                <select type="text" name="gender" id="" class="form-control border rounded" required>
                    <option value=""></option>
                    <option value="male" {{auth('student')->user()->gender == 'male' ? 'selected' : ''}}>MALE</option>
                    <option value="female" {{auth('student')->user()->gender == 'female' ? 'selected' : ''}}>FEMALE</option>
                </select>
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.place_of_birth')}}</label>
            <div class="col-md-9">
                <input type="text" name="pob" id="" class="form-control border rounded" value="{{auth('student')->user()->pob}}">
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.date_of_birth')}}</label>
            <div class="col-md-9">
                <input type="date" name="dob" id="" class="form-control border rounded" required value="{{auth('student')->user()->dob == null ? null : auth('student')->user()->dob->format('Y-m-d')}}">
            </div>
        </div>
        <div class="d-flex justify-content-end py-3">
            <input type="submit" class="btn btn-sm btn-primary" value="{{__('text.word_save')}}" id="">
        </div>
    </form>
 </div>
@endsection
@section('script')
    <script>
        let set_field_length = function(element){
            let min = 0, max = 0;
            let length= $(element).find("option:selected").data('length');
            console.log(length);
            if (length && length.constructor === Array){
                min = length[0];
                max = length.slice(-1);
            }
            else if(length){
                min = max = length;
            }else{min = max = 9;}

            $("#parent_phone").prop('minlength', min);
            $("#parent_phone").prop('maxlength', max);
            $("#parent_phone").val(null);
        }
    </script>
@endsection