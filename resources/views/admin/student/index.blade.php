@extends('admin.layout')

@section('section')
@php
    $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
@endphp

<div class="col-sm-12">
    <div class="col-lg-12">
        <form method="get">
            <div class="input-group-merge d-flex rounded border border-dark my-3">
                <select class="form-control col-sm-10" name="year">
                    <option></option>
                    @foreach (\App\Models\Batch::all() as $batch)
                        <option value="{{$batch->id}}" {{$batch->id == $year ? 'selected' : ''}}>{{$batch->name}}</option>
                    @endforeach
                </select>
                <input type="submit" value="{{__('text.word_get')}}" class="btn btn-sm btn-dark text-capitalize col-sm-2 text-center">
            </div>
        </form>
    </div>
    <div class=" my-3">
        <input class="form-control" id="search_field" placeholder="search by name or matricule">
    </div>
    <div class="">
        <div class=" ">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-stripped" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody id="table_body">
                    
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
    $('#search_field').on('keyup', function() {
        let value = $(this).val();
        url = '{{ route("search_students") }}';
        // console.log(url);
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'key': value
            },
            success: function(response) {
                let html = '';
                let k = 1;
                // console.log(response);
                response.forEach(element => {
                    // console.log(element);
                    html += `
                    <tr>
                        <td>${k++}</td>
                        <td>${element.name}</td>
                        <td>${element.matric}</td>
                        <td>${element.campus_name}</td>
                        <td>${element.class_name}</td>
                        <td class="d-flex justify-content-end  align-items-start text-capitalize">
                            <a class="btn btn-sm btn-primary m-1" href="${element.show_link}"><i class="fa fa-info-circle"> {{__('text.word_view')}}</i></a> |
                            <a class="btn btn-sm btn-success m-1" href="${element.edit_link}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a>|
                            <a onclick="event.preventDefault();
                                confirm('Are you sure you want to delete student: ${element.name}?') ? document.getElementById('${element.id}-delete').submit() : null" class=" btn btn-danger btn-sm m-1"><i class="fa fa-trash"> {{__('text.word_delete')}}</i></a>
                            <form id="${element.id}-delete" action="${element.delete_link}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                            <a class="btn btn-sm btn-warning m-1" onclick="confirm('Are you sure you want to reset pasword for ${element.name}?') ? $('#id_${element.id}').submit() : null"><i class="fa fa-edit"> {{__('text.reset_password')}}</i></a>|
                            <form action="${element.password_reset}" method="post" id="id_${element.id}" class="hidden">@csrf</form>
                            <a class="btn btn-sm btn-secondary m-1" onclick="change_state('${element.activate_link}','Your are about to change student status for ${element.name}')">
                                <i class="fa fa-cog">
                                     ${element.active == true ? "{{__('text.word_disactivate')}}" : "{{__('text.word_activate')}}"} 
                                </i>
                            </a>
                        </td>
                    </tr>
                    `.replace('__SID__', element.id).replace('__ACTIVE__', element.active);
                });
                $('#table_body').html(html);
            },
            error: function(e) {
                console.log(e)
            }
        })
    })

    let change_state = function(url, message){
        let reason = prompt(message+" Provide a reason for this:");
        if(reason != null){
            window.location = url+"?reason="+reason;
        }else{
            alert("You are not allowed to change the status of a student without a justified reason");
        }
    }

</script>
@endsection