@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="my-3">
            <label class="text-capitalize">@lang('text.word_department')</label>
            <div class="form-control">{{ $department->name??'' }}</div>
        </div>
        <div class="my-3">
            <label class="text-capitalize">@lang('text.word_course')</label>
            <input class="form-control" placeholder="search course by course title or course code" oninput="loadCourses(this)">
        </div>
        <div class="my-4">
            <table class="table">
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>@lang('text.word_title')</th>
                    <th>@lang('text.word_level')</th>
                    <th>@lang('text.word_semester')</th>
                    <th>@lang('text.word_status')</th>
                    <th></th>
                </thead>
                <tbody id="xtbody"></tbody>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let loadCourses = function(searchElement){
            let key = $(searchElement).val();
            let url = "{{ route('search_courses') }}";
            $.ajax({
                method: 'GET', url: url, data: {'name':key}, success: function(data){
                    console.log(data);
                    let counter = 1;
                    let html = ``;
                    for (const key in data) {
                        if (data.hasOwnProperty.call(data, key)) {
                            const element = data[key];
                            html += `
                            <tr>
                                <td>${counter++}</td>
                                <td>${element.name}</td>
                                <td>Level ${element._level}</td>
                                <td>${element._semester}</td>
                                <td>${element.status}</td>
                                <td>
                                    <form method="POST">
                                        @csrf
                                        <input type="hidden" name="school_unit_id" value="{{ $department->id }}">
                                        <input type="hidden" name="subject_id" value="${ element.id }">
                                        <button class="btn btn-sm rounded btn-primary" type="submit">@lang('text.word_add')</button>
                                    </form>
                                </td>
                            </tr>
                            `;
                        }
                    }
                    $('#xtbody').html(html);
                }
            })
        }
    </script>
@endsection