@extends('admin.layout')
@section('section')
<div class="py-3">
    <div id="picker_form" name="picker_form">
        <div class="d-flex input-group-merge border border-dark rounded my-3">
            <label for="" class="input-group-text px-3 text-center bg-dark text-light ">{{__('text.word_class')}}</label>
            <select name="class" class="form-control" required id="_class_" onchange="subjects()">
                <option value=""></option>
                @foreach(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                    <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-dark" onclick="subjects()">{{__('text.word_courses')}}</button>
        </div>
        <table class="table adv-table">
            <thead class="text-capitalize bg-dark">
                <th>{{__('text.sn')}}</th>
                <th>{{__('text.word_code')}}</th>
                <th>{{__('text.word_course')}}</th>
                <th>{{__('text.word_semester')}}</th>
                <th>{{__('text.word_action')}}</th>
            </thead>
            <tbody id="courses"></tbody>
        </table>
    </div>
</div>
@endsection
@section('script')
<script>
    function formFilled(){
        $('#actions').removeClass('hidden');
    }

    function _import(_class, _subject) {
        event.preventDefault();
        url = "{{route('admin.result.exam.import', ['_C_', '_S_'])}}";
        url = url.replace('_C_', _class);
        url = url.replace('_S_', _subject);
        window.location = url;

    }

    function _fill(_class, _subject) {
        event.preventDefault();
        url = "{{route('admin.result.exam.fill', ['_C_', '_S_'])}}";
        url = url.replace('_C_', _class);
        url = url.replace('_S_', _subject);
        window.location = url;

    }

    function subjects() {
        _class = $('#_class_').val();
        url = "{{route('class_subjects', '_C_')}}";
        url = url.replace('_C_', _class);
        $.ajax({
            method: 'GET',
            url: url,
            success:function(data){
                console.log(data);
                html = ``;
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                    html += `<tr class="border-bottom border-dark">
                                <td class="border-right border-light">`+ (1+index) +`</td>
                                <td class="border-right border-light">`+element.code+`</td>
                                <td class="border-right border-light">` +element.name+`</td>
                                <td class="border-right border-light">` +element.semester+`</td>
                                <td class="border-right border-light">
                                    <button onclick="_fill(`+_class+`, `+element.id+`)" class="btn btn-sm btn-success"">{{__('text.word_fill')}}</button>|
                                    <button onclick="_import(`+_class+`, `+element.id+`)" class="btn btn-sm btn-primary">{{__('text.word_import')}}</button>
                                </td>
                            </tr>`;
                }
                console.log(html);
                $('#courses').html(html);

            }
        })
    }
</script>
@endsection