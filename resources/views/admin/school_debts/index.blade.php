@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="col-lg-12">
        <div class="form-panel mb-5 mt-5 ml-2">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.debts.getStudentWithDebts')}}">
                <div class="form-group @error('class_id') has-error @enderror ml-2">
                    <div class="col-sm-3 d-flex">
                        <select name="section" class="form-control section" id="section0">
                            <option selected disabled>Select Section</option>
                            @if(isset($schoolUnits))
                                @foreach($schoolUnits as $key => $option)
                                    <option value="{{$key}}">{{$option}}</option>
                                @endforeach
                            @else
                                <option>No Sections Created</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-3 d-flex">
                        <select class="form-control" name="type">
                            <option value="">School Section</option>
                            <option value="day">Day Section</option>
                            <option value="boarding">Boarding Section</option>
                        </select>
                        @error('type')
                            {{ $message }}
                        @enderror
                    </div>

                    <div class="col-sm-3">
                        <select class="form-control" name="batch_id">
                            <option value="">Select Year</option>
                            @foreach($years as $key => $year)
                             <option value="{{$year->id}}">{{$year->name}}</option>
                            @endforeach
                        </select>
                        @error('batch_id')
                        {{ $message }}
                        @enderror
                    </div>
                    <div class=" col-sm-3 mb-1 d-flex justify-content-end">
                        <button class="btn btn-xs btn-primary" id="submit" type="submit">Get Students</button>
                    </div>
                </div>
                @csrf
            </form>
        </div>
    </div>
    <div class="">
        <div class=" ">
            <table cellpadding="0" cellspacing="0" border="0" class="" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Matricule</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach($students as $k=>$student)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$student->name}}</td>
                        <td>{{$student->matric}}</td>
                        <td>{{$student->email}}</td>
                        <td>{{$student->phone}}</td>
                        <td class="d-flex justify-content-end  align-items-start">
                            <a class="btn btn-sm btn-primary m-1" href="{{route('admin.debts.showDebts',[$student->id])}}"><i class="fa fa-info-circle"> View</i></a> |
                            <a class="btn btn-sm btn-success m-1" href="{{route('admin.debts.collectDebts',[$student->id])}}"><i class="fa fa-edit"> Collect Debts</i></a>|
                        </td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
            <div class="d-flex justify-content-end">

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $('.section').on('change', function() {
        let value = $(this).val();
        url = '{{ route("admin.getSections", ":id") }}';
        search_url = url.replace(':id', value);
        console.log(search_url);
        $.ajax({
            type: 'GET',
            url: search_url,
            data: {
                'parent_id': value
            },
            success: function(response) {
                let size = response.data.length;
                let data = response.data;
                let html = "";
                if (size > 0) {
                    html += '<div><select class="form-control"  name="' + data[0].id + '" >';
                    html += '<option selected> Select Circle</option>'
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    html += '</select></div>';
                } else {
                    html += '<div><select class="form-control"  >';
                    html += '<option selected> No data is avalaible</option>'
                    html += '</select></div>';
                }
                $('.circle').html(html);
            },
            error: function(e) {
                console.log(e)
            }
        })
    })
    $('#circle').on('change', function() {

        let value = $(this).val();
        url = "{{route('admin.getClasses', "
        VALUE ")}}";
        search_url = url.replace('VALUE', value);
        $.ajax({
            type: 'GET',
            url: search_url,
            success: function(response) {
                let size = response.data.length;
                let data = response.data;
                let html = "";
                if (size > 0) {
                    html += '<div><select class="form-control"  name="' + data[0].id + '" >';
                    html += '<option selected> Select Class</option>'
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    html += '</select></div>';
                } else {
                    html += '<div><select class="form-control"  >';
                    html += '<option selected> No data is avalaible</option>'
                    html += '</select></div>';
                }
                $('.class').html(html);
            },
            error: function(e) {
                console.log(e)
            }
        })
    })
</script>
@endsection
