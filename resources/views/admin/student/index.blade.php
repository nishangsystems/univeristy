@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="col-lg-12">
        
    </div>
    <div class="">
        <div class=" ">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-stripped" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th>{{__('text.word_email')}}</th>
                        <th>{{__('text.word_phone')}}</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $k=>$student)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$student->name}}</td>
                        <td>{{$student->matric}}</td>
                        <td>{{$student->email}}</td>
                        <td>{{$student->phone}}</td>
                        <td class="d-flex justify-content-end  align-items-start text-capitalize">
                            <a class="btn btn-sm btn-primary m-1" href="{{route('admin.student.show',[$student->id])}}"><i class="fa fa-info-circle"> {{__('text.word_view')}}</i></a> |
                            <a class="btn btn-sm btn-success m-1" href="{{route('admin.student.edit',[$student->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a>|
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-1"><i class="fa fa-trash"> {{__('text.word_delete')}}</i></a>
                            <form id="delete" action="{{route('admin.student.destroy',[$student->id])}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
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
                console.log(response);
                let size = response.data.length;
                let data = response.data;
                let html = "";
                if (size > 0) {
                    html += '<div><select class="form-control text-capitalize"  name="' + data[0].id + '" >';
                    html += '<option disabled> {{__("text.select_circle")}}</option>'
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    html += '</select></div>';
                } else {
                    html += '<div><select class="form-control"  >';
                    html += '<option disabled> {{__("text.no_data_available")}}</option>'
                    html += '</select></div>';
                }
                $('#circle').html(html);
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
                    html += '<div><select class="form-control text-capitalize"  name="' + data[0].id + '" >';
                    html += '<option disabled> {{__("text.select_class")}}</option>'
                    for (i = 0; i < size; i++) {
                        html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    html += '</select></div>';
                } else {
                    html += '<div><select class="form-control text-capitalize"  >';
                    html += '<option disabled> {{__("text.no_data_available")}}</option>'
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
