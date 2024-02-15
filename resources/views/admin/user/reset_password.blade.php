@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="container-fluid my-3">
            <input class="form-control rounded" type="text" placeholder="search user by name, email, matricule or username" oninput="runsearch(this)">
        </div>
        <hr class="my-2">
        <table class="table adv-table">
            <thead class="text-capitalize fw-semibold">
                <th>#</th>
                <th>@lang('text.word_name')</th>
                <th>@lang('text.word_email')</th>
                <th>@lang('text.word_username')</th>
                <th>@lang('text.word_matricule')</th>
                <th></th>
            </thead>
            <tbody id="search_results">
                
            </tbody>
        </table>
    </div>
@endsection
@section('script')
    <script>
        let runsearch = function(searchElement){
            let text = $(searchElement).val();
            let url = "{{ route('admin.users.search') }}";
            $.ajax({
                method: "GET", url: url, data: {'key': text}, success: function(data){
                    let html = '';
                    let k = 1;
                    data.users.forEach(user=>{
                        html += `
                            <tr>
                                <td>${k++}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.username}</td>
                                <td>${user.matric}</td>
                                <td>
                                    <form method="post">
                                        @csrf
                                        <input type="hidden" name="user_id" value="${user.id}" required>
                                        <button type="submit" class="btn btn-primary btn-xs rounded">{{ __('text.reset_password') }}</button>
                                    </form>
                                </td>
                            </tr>
                            `;
                    });
                    $('#search_results').html(html);
                }
            });
        }
    </script>
@endsection