@extends('admin.layout')

@section('section')
<div class="col-sm-12">

    <div class="my-3">
        <input class="form-control" id="search" placeholder="Search Student by Name or Matricule" required name="student_id" />
    </div>

    <div class="content-panel">
        <div class="table-responsive">
            <table class="table-bordered">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th>{{__('text.word_program')}}</th>
                        <th>{{__('text.word_gender')}}</th>
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
        val = $(this).val();
        url = "{{route('admin.searchStudent',':id')}}";
        search_url = url.replace(':id', val);
        $.ajax({
            type: 'GET',
            url: search_url,
            success: function(response) {
                let html = new String();
                let size = response.data.length;
                let data = response.data;
                for (i = 0; i < size; i++) {
                    html += '<tr>' +
                        '    <td>' + (i + 1) + '</td>' +
                        '    <td>' + data[i].name + '</td>' +
                        '    <td>' + data[i].matric + '</td>' +
                        '    <td>' + data[i].class + '</td>' +
                        '    <td>' + data[i].gender + '</td>' +
                        '    <td>' + data[i].campus + '</td>' +
                        '    <td class="d-flex justify-content-between align-items-center">' +
                        '        <a class="btn btn-xs btn-primary text-capitalize" href="' + data[i].link2 + '">{{__("text.award_scholarship")}}</a>' +
                        '    </td>' +
                        '</tr>';
                }
                $('#content').html(html);

            },
            error: function(e) {
                console.log(e)
            }
        })
    })
</script>
@endsection