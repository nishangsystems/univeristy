@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="py-2">
            <input class="form-control" type="text" placeholder="search student by matric or name" oninput="searchstudent(this)">
        </div>
        <table class="table">
            <thead class="text-capitalize">
                <th></th>
                <th>@lang('text.word_name')</th>
                <th>@lang('text.word_matricule')</th>
                <th>@lang('text.word_email')</th>
                <th></th>
            </thead>
            <tbody id="search_data">
                
            </tbody>
        </table>
    </div>
@endsection
@section('script')
    <script>
        let searchstudent = function(inputElement){
            let searchKey = $(inputElement).val();
            let url = "{{ route('search_students.per_campus_class_per_year') }}";
            $.ajax({
                method: 'get', url: url, 
                data: {
                    'key': searchKey,
                    'campus_id': '{{ $delegate->campus_id }}',
                    'year_id': '{{ $delegate->year_id }}',
                    'class_id': '{{ $delegate->class_id }}'
                }, 
                success: function(data){
                    console.log(data);
                    let html = ``;
                    let counter = 1;
                    for (const key in data) {
                        if (Object.hasOwnProperty.call(data, key)) {
                            const element = data[key];
                            html += `<tr>
                                    <td>${counter++}</td>
                                    <td>${element.name}</td>
                                    <td>${element.matric}</td>
                                    <td>${element.email}</td>
                                    <td>
                                        <form action="{{ route('admin.delegates.update',  $delegate->id ) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="${element.id}">
                                            <button type="submit" class="btn btn-sm rounded btn-primary">{{ __('text.word_assign') }}</button>
                                        </form>
                                    </td>
                                </tr>`;
                        }
                    }
                    $('#search_data').html(html);
                }
            });
        }
    </script>
@endsection