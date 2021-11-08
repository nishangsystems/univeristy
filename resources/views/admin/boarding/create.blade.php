@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.boarding_fee.store')}}">
            @csrf
            <div class="form-group @error('boarding_type') has-error @enderror">
                <label for="cname" class="control-label col-sm-2">School Section<span style="color:red">*</span></label>
                <div class="form-group @error('boarding_type') has-error @enderror col-md-10 ml-1">
                    <select class="form-control section" name="boarding_type">
                        <option value="">Select Section</option>
                        @foreach ($main_sections as $section)
                            <option value="{{$section->id}}">{{$section->name}}</option>
                        @endforeach
                    </select>
                    @error('boarding_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div id="children"></div>
                {{-- <div id="children1" ></div> --}}
            </div>
            <div class="form-group @error('amount_new_student') has-error @enderror">
                <label for="cname" class="control-label col-sm-2">Amount: New Students <span style="color:red">*</span></label>
                <div class="col-md-10">
                    <input class=" form-control" name="amount_new_student" value="{{old('amount_new_student')}}" type="number" required />
                    @error('amount_new_student')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('amount_old_student') has-error @enderror">
                <label for="cname" class="control-label col-sm-2">Amount: Old Students <span style="color:red">*</span></label>
                <div class="col-md-10">
                    <input class=" form-control" name="amount_old_student" value="{{old('amount_old_student')}}" type="number" required />
                    @error('amount_old_student')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-sm btn-primary mx-3" type="submit">Save</button>
                    <a class="btn btn-sm btn-danger" href="#" type="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $('.section').on('change', function() {
       refresh($(this))
    })




    function refresh(div){
        val = div.val();
        url = "{{route('admin.getSubUnits', ':id')}}";
        search_url = url.replace(':id', val);
        $.ajax({
            type: 'GET',
            url: search_url,
            success: function(response) {
                let html = "";
                let size = response.length;
                if(response.length > 0){
                    html+= '<div class="col-md-2"></div>'
                    html += '<div class="col-md-10"><select class="form-control section1"  name="boarding_type">';
                    html+='<option value="">'+'Select section'+'</option>';
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + response[i].id + '">' + response[i].name + '</option>';
                    }
                    html+= '</select><div id="children1"></div></div>';
                }
                $('#children').html(html)
            },
            error: function(e) {

            }
        })
    }


    function refresh1(div){
        val = div.val();

        url = "{{route('admin.getSubUnits', ':id')}}";
        search_url = url.replace(':id', val);
        search_url = search_url.replaceAll(" ", "");
        console.log(search_url)
        $.ajax({
            type: 'GET',
            url: search_url,
            success: function(response) {
                let html = "";
                let size = response.length;
                console.log(response)
                if(response.length > 0){
                   html+= '<div class="col-md-1"></div>'
                    html += '<div class="col-lg-12 mt-3 p-2"><select class="form-control" name="boarding_type">';
                    html+='<option value="">'+'Select section'+'</option>';
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + response[i].id + '">' + response[i].name + '</option>';
                    }
                    html+= '</select></div>';
                }
                $('#children1').html(html)
            },
            error: function(e) {

            }
        })
    }
</script>
@endsection
