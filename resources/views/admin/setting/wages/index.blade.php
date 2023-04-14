@extends('admin.layout')
@section('section')
    <div class="py-4">
        <div class="py-2">
            <input class="form-control" id="search_teacher" placeholder="search teacher by name, matricule or username">
            <div class="py-2">
            <table>
                <thead class="text-capitalize">
                    <th class="border">##</th>
                    <th class="border">{{__('text.word_name')}}</th>
                    <th class="border">{{__('text.word_matricule')}}</th>
                    <th class="border">{{__('text.word_email')}}</th>
                    <th class="border">{{__('text.word_username')}}</th>
                    <th class="border"></th>
                </thead>
                <tbody id="teachers_table"></tbody>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var teachers = [];
        $('.filter').on('input', function(){
            $('#filter_form').submit();
        })
        let search_t = function search_teacher() {
            let val = $('#search_teacher').val();
            url = "{{route('admin.users.search')}}";
            $.ajax({
                method: 'get',
                url: url,
                data: {key : val},
                success: function(data){
                    teachers = data.users;
                    // console.log(data.users);
                    html = '';
                    for (const key in data.users) {
                        if (data.users.hasOwnProperty.call(data.users, key)) {
                            const element = data.users[key];
                            // console.log(element);
                            html += `<tr>
                                    <td class="border-left border-right">${key+1}</td>
                                    <td class="border-left border-right">${element.name}</td>
                                    <td class="border-left border-right">${element.matric}</td>
                                    <td class="border-left border-right">${element.email}</td>
                                    <td class="border-left border-right">${element.username}</td>
                                    <td class="border-left border-right"><a class="btn btn-sm btn-primary teacher_selector" id="user_id_${element.id}" href="{{route('admin.users.wages.create', '__TID__')}}">{{__('text.set_wages')}}</a></td>
                                </tr>`.replace('__TID__', element.id);
                        }
                    }
                    $('#teachers_table').html(html);
                }
            })
        }

        $('#search_teacher').on('input', search_t);
        function selected(user_id){
            // user_id = $(this).attr('user_id');
            // alert(user_id);
            let teacher = teachers.filter((tch)=>{return tch.id == user_id;})[0];
            console.log(teacher);
            html = `<tr>
                        <td class="border-left border-right">1</td>
                        <td class="border-left border-right">${teacher.name}</td>
                        <td class="border-left border-right">${teacher.matric}</td>
                        <td class="border-left border-right">${teacher.email}</td>
                        <td class="border-left border-right">${teacher.username}</td>
                        <td class="border-left border-right"><input type="radio" checked ><input type="hidden" name="teacher_id", value="${teacher.id}"></td>
                    </tr>`;
            
            $('#teachers_table').html(html);
        }
    </script>
@endsection