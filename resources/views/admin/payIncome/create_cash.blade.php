@extends('admin.layout')

@section('section')
<div class="col-sm-12">

    @if(request()->has('us') && !(request('us') == null))
    <div class="py-3">
        <form action="{{Request::url()}}/save" method="get">
            <div class="row my-2">
                <label for="" class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_user')}}</label>
                <div class="col-md-9 col-lg-9">
                    <input type="hidden" name="user_id" value="{{request('us')}}">
                    <span class="form-control text-capitalize">{{\App\Models\User::find(request('us'))->name.' - '.\App\Models\User::find(request('us'))->email.' - '.\App\Models\User::find(request('us'))->type}}</span>
                </div>
            </div>
            <div class="row my-2">
                <label for="" class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_name')}}</label>
                <div class="col-md-9 col-lg-9">
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            <div class="row my-2">
                <label for="" class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_amount')}}</label>
                <div class="col-md-9 col-lg-9">
                    <input type="number" name="amount" class="form-control" required>
                </div>
            </div>
            <div class="row my-2">
                <label for="" class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_description')}}</label>
                <div class="col-md-9 col-lg-9">
                    <textarea rows="3" name="description" class="form-control"></textarea>
                </div>
            </div>
            <div class="my-2 d-flex justify-content-end">
                <a href="{{route('admin.home')}}" class="btn btn-sm btn-warning">{{__('text.word_cancel')}}</a>|
                <button type="submit" class="btn btn-sm btn-primary">{{__('text.word_save')}}</button>
            </div>
        </form>
    </div>
    @else
    <div class="my-3">
        <input class="form-control" id="search" placeholder="Search user by Name, Username or Email" required name="student_id" />
    </div>


    <div class="content-panel">
        <div class="table-responsive">
            <table class="table-bordered">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_email')}}</th>
                        <th>{{__('text.word_gender')}}</th>
                        <th>{{__('text.word_type')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="content">

                </tbody>
            </table>

        </div>
    </div>
    @endif
</div>
@endsection
@section('script')
<script>
    $('#search').on('keyup', function() {
        val = $(this).val();
        url = "{{route('admin.get_searchUser')}}";
        // search_url = url.replace(':id', val);
        $.ajax({
            type: 'GET',
            data: {'name': val},
            url: url,
            success: function(response) {
                let html = new String();
                // console.log(response);
                let size = response.data.length;
                let data = response.data;
                for (i = 0; i < size; i++) {
                    html += '<tr>' +
                        '    <td>' + (i + 1) + '</td>' +
                        '    <td>' + data[i].name + '</td>' +
                        '    <td>' + data[i].email + '</td>' +
                        '    <td>' + data[i].gender + '</td>' +
                        '    <td>' + data[i].type + '</td>' +
                        '    <td class="d-flex justify-content-between align-items-center">' +
                        '        <a class="btn btn-xs btn-primary text-capitalize" href="' + data[i].link + '"> {{__("text.collect_income")}}</a>' +
                        '    </td>' +
                        '</tr>';
                }
                $('#content').html(html);

            },
            error: function(e) {
                console.log(e)
            }
        })
    })
</script>
@endsection