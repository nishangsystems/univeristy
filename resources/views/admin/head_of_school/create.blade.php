@extends('admin.layout')
@section('section')
    <div class="my-3">
        @if(request('school_unit_id') == null)
        <form class="container-fluid">
            <div class="card border-0">
                <div class="card-body py-4 px-2">
                    <div class="my-2">
                        <div class="text-secondary fs-4 text-capitalize my-1">@lang('text.word_school'): <div>
                        <select class="form-control text-uppercase" name="school_unit_id" required onchange="indicate_hos(this)">
                            <option></option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }} {{ old('school_unit_id', request('school_unit_id')) == $school->id ? 'selected' : '' }}"> School of {{ $school->name }}</option>    
                            @endforeach
                        </select>
                    </div>


                    @if(request('school_unit_id') == null)
                    <div class="my-2 d-flex justify-content-end">
                        <button class="btn btn-primary rounded btn-sm" type="submit">@lang('text.word_next')</button>
                    </div>
                    @endif

                </div>
            </div>
        </form>
        @endif


        @if(request('school_unit_id') != null)
            <div class="my-2">
                <div class="text-secondary fs-4 text-capitalize my-1">@lang('text.active_head_of_school'): <div>
                <label class="form-control text-uppercase" id="hos_hint">{{ $hos->name??'' }} | {{ $hos->email??'' }}</label>
            </div>
            <input class="form-control rounded my-5 input-lg text-center" placeholder="search user by name, email, matricule or username" oninput="runsearch(this)">
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
        @endif
        
    </div>
@endsection
@section('script')
    <script>
        let indicate_hos = function(element){
            let userInfo = JSON.parse($(element.options[element.getSelectedIndex]).attr('data-hos'));
            console.log(userInfo);
            if(userInfo != null && userInfo.length > 0){
                // show user info
                let text = "name : "+userInfo['name']+" | email : "+userInfo['email'];
                $('#hos_hint').text(text);
            }
        }

        let runsearch = function(searchElement){
            let text = $(searchElement).val();
            let url = "{{ route('admin.users.search') }}";
            $.ajax({
                method: "GET", url: url, data: {'key': text}, success: function(data){
                    console.log(data);
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
                                    <form method="post" action="{{ route('admin.headOfSchools.create') }}">
                                        @csrf
                                        <input type="hidden" name="user_id" value="${user.id}" required>
                                        <input type="hidden" name="school_unit_id" value="{{ request('school_unit_id') }}" required>
                                        <button type="submit" class="btn btn-primary btn-xs rounded">{{ __('text.word_create') }}</button>
                                    </form>
                                </td>
                            </tr>
                            `;
                    });
                    $('#search_results').html(html);
                }
            })
        }
    </script>
@endsection