@extends('admin.layout')
@section('script')
    <script>
        let getStudents = (element)=>{
            let _key = $(element).val();
            let _url = "{{ route('search_students') }}";
            $.ajax({
                method: 'GET', url: _url, data: {key: _key}, success: function(data){
                    console.log(data);
                    let html = '';
                    let counter = 1;
                    data.forEach(element=>{
                        html += `
                            <tr class="border-bottom">
                                <td>${counter++}</td>
                                <td>${element.name}</td>
                                <td>${element.matric}</td>
                                <td>${element.class_name}</td>
                                <td><a class="btn btn-sm btn-primary" href="{{ route('admin.resits.payments.record', ['resit_id'=>$resit->id, 'student_id'=>'__STID__']) }}">{{ __('text.record_payment') }}</a></td>
                            </tr>
                        `.replace('__STID__', element.id);
                    });
                    $('#student_listing_table').html(html);
                }
            });
        };
    </script>
@endsection
@section('section')
    <div class="py-3">
        <div class="container-fluid">
            <div class="py-3 mb-5">
                <div class="text-capitalize text-secondary">{{ trans_choice('text.word_student', 1) }}</div>
                <input class="form-control rounded" oninput="getStudents(this)" placeholder="search student by matricule, name or email">
            </div>
            <hr>
            <table class="table">
                <thead class="text-capitalize">
                    <th>@lang('text.sn')</th>
                    <th>@lang('text.word_name')</th>
                    <th>@lang('text.word_matricule')</th>
                    <th>@lang('text.word_class')</th>
                    <th></th>
                </thead>
                <tbody id="student_listing_table"></tbody>
            </table>
        </div>
    </div>
@endsection
