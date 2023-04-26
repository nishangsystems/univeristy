@extends('admin.layout')
@section('section')
    <form class="py-5 px-3" method="get">
        <select class="form-control" name="background_id" required>
            <option>{{__('text.select_background')}}</option>
            @foreach (\App\Models\Background::all() as $bg)
                <option value="{{$bg->id}}" {{request('background_id') == $bg->id ? 'selected' : ''}}>{{$bg->background_name}}</option>
            @endforeach
        </select>
        <div class="d-flex justify-content-end py-3">
            <input type="submit" value="{{__('text.word_courses')}}" class="btn btn-xs btn-primary">
        </div>
    </form>
    @if (request('background_id') != null)
        <div class="py-5 px-3">
            <input type="text" name="level" class="form-control border input rounded my-4" id="modal-level" placeholder="{{__('text.prase_course_search')}}" oninput="getCourses('#modal-level')">
            <table>
                <thead class="text-capitalize bg-light text-primary">
                    <th class="border-left border-right">{{__('text.course_code')}}</th>
                    <th class="border-left border-right">{{__('text.course_title')}}</th>
                    <th class="border-left border-right d-none d-md-table-cell">{{__('text.credit_value')}}</th>
                    <th class="border-left border-right d-none d-md-table-cell">{{__('text.word_status')}}</th>
                    <th class="border-left border-right">{{__('text.word_action')}}</th>
                </thead>
                <tbody id="modal_table"></tbody>
            </table>
        </div>

    @endif
@endsection
@section('script')
<script>
    let class_courses = [];

    function getCourses(div){
        
        value = $(div).val();
        // console.log('____');
        url = "{{route('admin.search_course')}}";
        $.ajax({
            method:"GET",
            url: url,
            data: {'value' : value},
            success: function(data){
                console.log(data);
                class_courses = data.data;
                let html = ``;
                for (const key in data.data) {
                    // if(registered_courses.filter(e => e['id'] == data.data[key]['id']).length > 0){continue;}
                    html += `<tr class="border-bottom" id="modal-`+data.data[key]['id']+`">
                            <td class="border-left border-right">`+data.data[key]['code']+`</td>
                            <td class="border-left border-right">`+data.data[key]['name']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+data.data[key]['cv']+`</td>
                            <td class="border-left border-right d-none d-md-table-cell">`+data.data[key]['status']+`</td>
                            <td class="border-left border-right">
                                <span class="btn btn-sm btn-primary" onclick="$('#_form_`+data.data[key]['id']+`').submit()">{{__('text.word_add')}}</span>
                                <form method="get" class="hidden" id="_form_`+data.data[key]['id']+`" action="{{Request::url()}}/save">                                    
                                    <input type="hidden" name="background_id" value="{{request('background_id')}}">
                                    <input type="hidden" name="course_code" value="`+data.data[key]['code']+`">
                                </form>
                            </td>
                        </tr>`
                }
                $('#modal_table').html(html);
            }
        })
    }
</script>
@endsection