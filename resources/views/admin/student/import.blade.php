@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel row">
            <form class="form-horizontal col-md-6 col-lg-8 border-right" enctype="multipart/form-data" role="form" method="POST" action="{{route('admin.students.import')}}">
                @csrf
                <div class="form-group @error('section') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_batch')}}</label>
                    <div class="col-lg-10">
                        <div>
                            <select class="form-control" required name="batch">
                                <option  disabled>{{__('text.select_year')}}</option>
                                @forelse(\App\Models\Batch::orderBy('name')->get() as $section)
                                    <option {{old('batch') == $section->id?'selected':''}} value="{{$section->id}}">{{$section->name}}</option>
                                @empty
                                    <option>{{__('text.no_batch_created')}}</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>


                <div class="form-group @error('file') has-error @enderror text-capitalize">
                    <label for="cname" class="control-label col-lg-2">{{__('text.excel_file')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="file" value="{{old('file')}}" type="file" required/>
                        @error('file')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h5 class="mt-5 mb-4 font-weight-bold text-capitalize">{{__('text.admission_class_information')}}</h5>

                <div class="form-group text-capitalize">
                    <label for="cname" class="control-label col-lg-2">{{__('text.word_campus')}}</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="campus_id" onchange="loadPrograms(event.target)" required>
                            <option selected disabled>{{__('text.select_campus')}}</option>
                            @forelse(\App\Models\Campus::all() as $section)
                                <option value="{{$section->id}}">{{$section->name}}</option>
                            @empty
                                <option>{{__('text.no_data_available')}}</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div id="section">
                    <div class="form-group text-capitalize">
                        <label for="cname" class="control-label col-lg-2">Program</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" name="program_id" id="program_id" required>
                                
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
                    </div>
                </div>

            </form>
            <div class="col-md-6 col-lg-4 py-3 px-2">
                <div class="text-center text-capitalize text-primary py-3">File Format</div>
                <table class="bg-light">
                    <thead class="text-capitalize bg-dark text-light fs-6">
                        <th>name</th>
                        <th>matric</th>
                        <th>email</th>
                        <th>gender</th>
                    </thead>
                    <tbody>
                        @for($i=0; $i < 4; $i++)
                        <tr class="border-bottom">
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
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
                    options += `<option value="`+element.id+`">`+element.program+` : Level `+element.level+`</option>`;
                });
                $('#program_id').html(options);
            }
        })
    }
</script>
@endsection
