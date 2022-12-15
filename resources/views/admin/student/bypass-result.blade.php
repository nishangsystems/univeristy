@extends('admin.layout')

@section('section')
<div class="py-3">

    @if(!request('student_id') == null)

        <form action="{{Request::url()}}/set" method="get">
            <div class="row my-2">
                <label for="" class="text-capitalize col-md-3">{{__('text.word_reason')}}</label>
                <div class="col-sm-9">
                    <textarea name="bypass_result_reason" class="form-control" id="" rows="3"></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-end my-2">
                <input type="submit" name="" id="" class="btn btn-sm btn-primary" value="{{__('text.word_save')}}">
            </div>
        </form>

    @else

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
                        <th>{{__('text.word_class')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="content">

                </tbody>
            </table>

        </div>
    </div>
    @endif
</div>
@endsection
@section('script')
<script>
    $('#search').on('keyup', function() {
        val = $(this).val();
        url = "{{route('admin.get_searchStudent')}}";
        // search_url = url.replace(':id', val);
        $.ajax({
            type: 'GET',
            data: {'name': val},
            url: url,
            success: function(response) {
                let html = new String();
                console.log(response);
                let size = response.data.length;
                let data = response.data;
                for (i = 0; i < size; i++) {
                    html += '<tr>' +
                        '    <td>' + (i + 1) + '</td>' +
                        '    <td>' + data[i].name + '</td>' +
                        '    <td>' + data[i].matric + '</td>' +
                        '    <td>' + data[i].class + '</td>' +
                        '    <td>' + data[i].campus + '</td>' +
                        '    <td class="d-flex justify-content-between align-items-center">' +
                        '        <a class="btn btn-xs btn-primary text-capitalize" href="{{Request::url()}}/'+data[i].id+'"> {{__("text.word_bypass")}}</a>' +
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