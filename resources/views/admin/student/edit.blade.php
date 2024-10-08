@extends('admin.layout')

@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.student.update',$student->id)}}">
                <input name="type" value="{{request('type','teacher')}}" type="hidden"/>
                <input name="_method" value="put" type="hidden"/>
                <input name="year" value="{{$student->admission_batch_id}}" type="hidden"/>
                <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.personal_information')}}</h5>
                @csrf
                <div class="form-group @error('name') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.full_name')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="name" value="{{old('name', $student->name)}}" type="text" required/>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group @error('matric') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_matricule')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="matric" value="{{old('matric', $student->matric)}}" type="text" required/>
                        @error('matric')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <?php 
            /*<div class="form-group @error('email') has-error @enderror">
                <label for="email" class="control-label col-lg-2 text-capitalize">{{__('text.word_email')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="email" value="{{old('email')}}" type="email"  />
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>*/
            ?>

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

                <div class="form-group @error('dob') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.date_of_birth')}}</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="dob" value="{{old('dob', ($student->dob != null ? $student->dob->format('Y-m-d') : null))}}" type="date"  />
                        @error('dob')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('pob') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.place_of_birth')}}</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="pob" value="{{old('pob', $student->pob)}}" type="text"  />
                        @error('pob')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('program_status') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.program_status')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control text-capitalize" name="program_status">
                            <option disabled>{{__('text.select_gender')}}</option>
                            @foreach ($status_set as $stats)
                                <option {{old('program_status', $student->program_status) == $stats->name ? 'selected':''}} value="{{ $stats->name }}">{{ $stats->name }}</option>
                            @endforeach
                        </select>
                        @error('program_status')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group @error('gender') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_gender')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control text-capitalize" name="gender">
                            <option disabled>{{__('text.select_gender')}}</option>
                            <option {{old('gender', $student->gender) == 'male'?'selected':''}} value="male">{{__('text.word_male')}}</option>
                            <option {{old('gender', $student->gender) == 'female'?'selected':''}} value="female">{{__('text.word_female')}}</option>
                        </select>
                        @error('gender')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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
    function loadPrograms(element){
        let val = element.value;
        url = "{{route('campus.programs', ['__V__'])}}";
        url =url.replace('__V__', val);
        $.ajax({
            method: 'get',
            url: url,
            success: function(data){
                let options = `<option value="">{{__('text.select_program')}}</option>`;
                data.forEach(element => {
                    console.log(element);
                    options += `<option {{old('program_id', $student->program_id)==`+element.id+` ? 'selected' : ''}} value="`+element.id+`">`+element.program+` : Level `+element.level+`</option>`;
                });
                $('#program_id').html(options);
            }
        })
    }
</script>
@endsection
