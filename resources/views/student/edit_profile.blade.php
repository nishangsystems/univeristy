@extends('student.layout')
@section('section')
 <div class="col-md-10 mx-auto py-4">
    <form action="{{route('student.update_profile')}}" method="post">
        @csrf
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_phone')}}</label>
            <div class="col-md-9">
                <input type="number" name="phone" id="" class="form-control" value="{{auth('student')->user()->phone ?? ''}}">
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.parents_phone_number')}}</label>
            <div class="col-md-9 input-group input-group-merge d-flex">
                <select class="ml-3 form-control" id="country_picker" name="parent_phone_code">
                    @foreach (config('country-phone-codes') as $code)
                        <option value="+{{ $code['code'] }}" {{ ($code['autoselect']??null) == 1 ? 'selected' : '' }}>{{ $code['country'].' (+'.$code['code'].')' }}</option>
                    @endforeach
                </select>
                <input type="text" name="parent_phone_number" id="parent_phone" class="form-control mr-3" value="{{auth('student')->user()->parent_phone_number ?? ''}}">
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_email')}}</label>
            <div class="col-md-9">
                <input type="email" name="email" id="" class="form-control" value="{{auth('student')->user()->email}}">
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.word_gender')}}</label>
            <div class="col-md-9">
                <select type="text" name="gender" id="" class="form-control" required>
                    <option value=""></option>
                    <option value="male" {{auth('student')->user()->gender == 'male' ? 'selected' : ''}}>MALE</option>
                    <option value="female" {{auth('student')->user()->gender == 'female' ? 'selected' : ''}}>FEMALE</option>
                </select>
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.place_of_birth')}}</label>
            <div class="col-md-9">
                <input type="text" name="pob" id="" class="form-control" value="{{auth('student')->user()->pob}}">
            </div>
        </div>
        <div class="row py-2">
            <label for="" class="col-md-3 text-capitalize">{{__('text.date_of_birth')}}</label>
            <div class="col-md-9">
                <input type="date" name="dob" id="" class="form-control" value="{{auth('student')->user()->dob}}">
            </div>
        </div>
        <div class="d-flex justify-content-end py-3">
            <input type="submit" class="btn btn-sm btn-primary" value="{{__('text.word_save')}}" id="">
        </div>
    </form>
 </div>
@endsection
@section('script')
    {{-- <script>
        let p_phone = '';
        $(document).ready(function(){
            p_phone = $('#parent_phone').val();
        })
        code_change = function(event){
            let val = event.target.value;
            $('#parent_phone').val(val+p_phone);
        }
    </script> --}}
@endsection