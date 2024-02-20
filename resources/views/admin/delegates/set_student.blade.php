@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="py-2">
            <input class="form-control" type="text" oninput="searchstudent(this)">
        </div>
        <table class="table">
            <thead class="text-capitalize">
                <th></th>
                <th>@lang('text.word_year')</th>
                <th>@lang('text.word_campus')</th>
                <th>@lang('text.word_class')</th>
                <th>@lang('text.word_student', 1)</th>
                <th>@lang('text.word_matricule')</th>
                <th></th>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
@endsection
@section('script')
    <script>
        let searchstudent = function(inputElement){
            let searchKey = $(inputElement).val();
            let url = "{{ route('search_students') }}";
            $.ajax({
                method: 'get', url: url, data: {'key': searchKey}, success: function(data){
                    console.log(data);
                }
            })
        }
    </script>
@endsection