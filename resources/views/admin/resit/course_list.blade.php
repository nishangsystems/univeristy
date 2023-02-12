@extends('admin.layout')
@section('section')
<div class="py-3">
    <input class="form-control border border-dark rounded" id="search_course" placeholder="search course by course code or course title" oninput="changed()">
</div>
<div class="py-2">
    <table class="table">
        <thead class="bg-secondary text-light text-capitalize">
                <th class="border-left border-right border-white">#</th>
                <th class="border-left border-right border-white">{{__('text.course_code')}}</th>
                <th class="border-left border-right border-white">{{__('text.course_title')}}</th>
                <th class="border-left border-right border-white">{{__('text.credit_value')}}</th>
                <th class="border-left border-right border-white">{{__('text.word_status')}}</th>
                <th class="border-left border-right border-white"></th>
        </thead>
        <tbody id="table_body">
        </tbody>
    </table>
</div>
@endsection
@section('script')
<script>
    function changed(){
        val = $('#search_course').val();
        url = "{{route('search_subjects')}}";
        $.ajax({
            method: 'get',
            data: {'name' : val},
            url: url,
            success: function(data){
                console.log(data);
                count = 1;
                html = '';

                for (let index = 0; index < data.data.length; index++) {
                    const element = data.data[index];
                    // console.log(element);
                    _html = `<tr class="border-bottom border-white">
                        <td class="border-left border-right border-white">${count++}</td>
                        <td class="border-left border-right border-white">${element.code}</td>
                        <td class="border-left border-right border-white">${element.name}</td>
                        <td class="border-left border-right border-white">${element.coef}</td>
                        <td class="border-left border-right border-white">${element.status}</td>
                        <td class="border-left border-right border-white">
                            <a class="btn btn-sm btn-primary" href="{{route('admin.resits.course_list.download', [request('resit_id'), '__CRS__'])}}">{{__('text.word_students')}}</a>
                        </td>
                    </tr>`;
                    
                    html += _html.replace('__CRS__', element.id);
                }
                $('#table_body').html(html);

            }
        })
    }
</script>
@endsection