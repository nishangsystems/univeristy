@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <div class="my-3">
            <input class="form-control" id="search" placeholder="Type student name to search" required/>
        </div>


        <div class="content-panel">
            <div class="table-responsive">
                <table class="table-bordered table">
                    <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th>{{__('text.total_fee')}}</th>
                        <th>{{__('text.word_paid')}}</th>
                        <th>{{__('text.word_balance')}}</th>
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
        $('#search').on('keyup', function () {

            val = $(this).val()
            url = "{{route('student-search', 'VALUE')}}";
            url = url.replace('VALUE', val);
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    console.log(data);
                    let html = "";
                    for (i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '    <td>'+(i+1)+'</td>' +
                            '    <td>'+data[i].name+'</td>' +
                            '    <td>'+data[i].class+'</td>' +
                            '    <td>'+data[i].total+'</td>' +
                            '    <td>'+data[i].paid+'</td>' +
                            '    <td>'+data[i].bal+'</td>' +
                            '    <td>'+data[i].campus+'</td>' +
                            '    <td class="d-flex justify-content-between align-items-center">' +
                            '        <a target="_blank" class="btn btn-xs btn-primary" href="'+data[i].rlink+'"> {{__("text.print_reciept")}}</a>' +
                            '    </td>' +
                            '</tr>';
                    }
                    $('#content').html(html)
                }, error: function (e) {
                }
            });
        });
    </script>
@endsection
