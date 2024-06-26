@extends('admin.layout')

@section('section')
<div class="col-sm-12">

    <div class="my-3">
        <input class="form-control" id="search" placeholder="Type student name to search" required />
    </div>


    <div class="content-panel">
        <div class="table-responsive">
            <table class="table-bordered">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="content">

                </tbody>
            </table>
            <div id="modal_box"></div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $('#search').on('keyup', function() {
        val = $(this).val()
        // val = val.replace('/', '\\/', val);
        console.log(val);
        url = "{{route('get-search-all-students')}}";
        url = url.replace(':id', val);
        $.ajax({
            type: "get",
            url: url,
            data: {'key' : val},
            success: function(data) {
                console.log(data)
                let html = "";
                for (i = 0; i < data.length; i++) {
                    html += `<tr>
                            <td> ${(i + 1)} </td>
                            <td> ${data[i].matric} </td>
                            <td> ${data[i].name} </td>
                            <td> ${data[i].campus} </td>
                            <td class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-xs btn-primary" href="{{ route('admin.clearance.fee.generate', '__SID__') }}" onclick="checkClearance(${data[i].id})"> {{__("text.fee_clearance")}}</button>
                            </td>
                        </tr>`.replace('__SID__', data[i].id);
                }
                $('#content').html(html)
            },
            error: function(e) {}
        });
    });

    let checkClearance = function(student_id){
        let clearance_url = "{{ route('admin.clearance.fee.generate', '__SID__') }}".replace('__SID__', student_id);
        let _url = "{{ route('admin.clearance.fee.check', '__STID') }}".replace('__STID', student_id);
        console.log(_url);
        $.ajax({
            url: _url, 
            method: 'GET', 
            success: function(data){
                console.log(data);
                if(data.data == null)
                    window.location = clearance_url;
                else{
                    let modal = `
                    <div id="modal-wizard" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div id="modal-wizard-container">
                                    <div class="modal-header">
                                        <h4 class="heading">CONFIRM RE-PRINT OF FEE CLEARANCE</h4>
                                    </div>

                                    <div class="modal-body step-content">
                                        <div class="container-fluid text-center caption">
                                            Only a single fee clearance is printed per student. Any more prints require payment from the concerned student. A fee clearance was last printed for this student on ${data.data.created_at}. 
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer wizard-actions">
                                    
                                    <a href="${clearance_url}" class="btn btn-success btn-sm btn-next">
                                        Confirm
                                        <i class="ace-icon fa fa-arrow-right icon-on-right"></i>
                                    </a>

                                    <button class="btn btn-danger btn-sm pull-left" onclick="$('#modal-wizard').hide()">
                                        <i class="ace-icon fa fa-times"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;

                    $('#modal_box').html(modal);
                    $('#modal-wizard').show();
                }
            }
        })
    }
</script>
@endsection
