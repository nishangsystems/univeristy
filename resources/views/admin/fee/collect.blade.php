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
                    html += '<tr>' +
                        '    <td>' + (i + 1) + '</td>' +
                        '    <td>' + data[i].matric + '</td>' +
                        '    <td>' + data[i].name + '</td>' +
                        '    <td>' + data[i].campus + '</td>' +
                        '    <td class="d-flex justify-content-between align-items-center">' +
                        '        <a class="btn btn-xs btn-primary" href="' + data[i].link + '"> {{__("text.fee_collections")}}</a>' +
                        '    </td>' +
                        '</tr>';
                }
                $('#content').html(html)
            },
            error: function(e) {}
        });
    });
</script>
@endsection
