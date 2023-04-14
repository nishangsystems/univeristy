@extends('admin.layout')
@section('section')
    <div class="py-4">
        <form method="get" id="filter_form" class=" py-3 mb-3 d-flex flex-wrap justify-content-around h4 text-capitalize">
            <div class="text-primary">
                <input type="radio" name="filter" class="filter" value="per_teacher" style="width: 2rem; height: 2rem; margin-right: 2rem;" {{request('filter') == 'per_teacher' ? 'checked' : ''}}>
                <label class="py-2">{{__('text.per_teacher')}}</label>
            </div>
            <div class="text-primary">
                <input type="radio" name="filter" class="filter" value="per_level" style="width: 2rem; height: 2rem; margin-right: 2rem;" {{request('filter') == 'per_level' ? 'checked' : ''}}>
                <label class="py-2">{{__('text.per_level')}}</label>
            </div>
            <div class="text-primary">
                <input type="radio" name="filter" class="filter" value="per_background_per_level" style="width: 2rem; height: 2rem; margin-right: 2rem;" {{request('filter') == 'per_background_per_level' ? 'checked' : ''}}>
                <label class="py-2">{{__('text.per_background_per_level')}}</label>
            </div>
        </form>
        <div class="py-3">
            <form class=" roundede py-4 px-3" method="post">
                @csrf
                <div class="py-2">
                    <label class="text-capitalize h4">{{__('text.word_background')}}</label>
                    <select class="form-control" name="background_id" required>
                        <option value=""></option>
                        @foreach (\App\Models\Background::all() as $bgd)
                            <option value="{{$bgd->id}}">{{$bgd->background_name}}</option>
                        @endforeach
                    </select>
                </div>
                @if (request('filter')=='per_teacher')
                    <div class="py-2">
                        <label class="text-capitalize h4">{{__('text.select_teacher')}}</label>
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
                @endif
                @if (request('filter')=='per_background_per_level')
                    <div class="py-2">
                        <label class="text-capitalize h4">{{__('text.select_teacher')}}</label>
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
                    <div class="py-2">
                        <label class="text-capitalize h4">{{__('text.select_level')}}</label>
                        <select class="form-control" name="level_id" required>
                            <option value=""></option>
                            @foreach (\App\Models\Level::all() as $lvl)
                                <option value="{{$lvl->id}}">{{$lvl->level}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if (request('filter') == 'per_level')
                    <div class="py-2">
                        <label class="text-capitalize h4">{{__('text.select_level')}}</label>
                        <select class="form-control" name="level_id" required>
                            <option value=""></option>
                            @foreach (\App\Models\Level::all() as $lvl)
                                <option value="{{$lvl->id}}">{{$lvl->level}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="py-2">
                    <label class="text-capitalize h4">{{__('text.hourly_rate')}}</label>
                    <input type="number" class="form-control" name="rate" required>
                </div>
                <div class="d-flex justify-content-end py-2">
                    <input type="submit" class="btn btn-sm btn-primary" value="{{__('text.word_save')}}">
                </div>
            </form>
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
                                    <td class="border-left border-right"><a class="btn btn-sm btn-primary teacher_selector" id="user_id_${element.id}" onclick="selected(${element.id})">{{__('text.word_select')}}</a></td>
                                </tr>`;
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