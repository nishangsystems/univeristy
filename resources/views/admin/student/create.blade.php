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
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_matricule')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="matric" value="{{old('matric')}}" type="text" required />
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
            <div class="form-group @error('pob') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.place_of_birth')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="pob" value="{{old('pob')}}" type="text"  />
                    @error('pob')
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
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.academic_year')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class=" form-control" name="admission_batch_id" required>
                        <option value="">select academic year</option>
                        @forelse(\App\Models\Batch::all() as $batch)
                            <option {{ \App\Helpers\Helpers::instance()->getCurrentAccademicYear() == $batch->id ? 'selected' : ''}} value="{{$batch->id}}">{{$batch->name}}</option>
                        @empty
                            <option value="" selected>No data found</option>
                        @endforelse
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('campus_id') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_campus')}} </label>
                <div class="col-lg-10">
                    <select name="campus_id" class="form-control" id="" onchange="loadPrograms(event.target)">
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

            <div class="form-group @error('program_id') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_program')}}</label>
                <div class="col-lg-10">
                    <select class=" form-control" name="program_id" id="program_id" required>
                    </select>
                    @error('program_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
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
<script>
    function loadPrograms(element){
        let val = element.value;
        url = "{{route('campus.programs', ['__V__'])}}";
        url =url.replace('__V__', val);
        $.ajax({
            method: 'get',
            url: url,
            success: function(data){
                data.sort((a, b)=>{
                    if (a.program > b.program) { return 1;}
                    if (a.program < b.program) { return -1;}
                    return 0;
                })
                let options = `<option value="">{{__('text.select_program')}}</option>`;
                data.forEach(element => {
                    console.log(element);
                    options += `<option value="`+element.id+`">`+element.program+` : Level `+element.level+`</option>`;
                });
                $('#program_id').html(options);
            }
        })
    }
</script>
@endsection

