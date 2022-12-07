@extends('admin.layout')

@section('section')
<div class="col-sm-12">

    <div class="py-3">
        <form action="{{Request::url()}}/save" method="get">
            <div class="row my-2">
                <label for="" class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_title')}}</label>
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
                <label for="" class="col-md-3 col-lg-3 text-capitalize">{{__('text.word_date')}}</label>
                <div class="col-md-9 col-lg-9">
                    <input type="date" name="date" class="form-control">
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
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_amount')}} ({{__('text.currency_cfa')}})</th>
                        <th>{{__('text.word_description')}}</th>
                        <th>{{__('text.word_date')}}</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Income::where('cash', true)->orderBy('id', 'DESC')->get() as $k=>$income)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$income->name ?? ''}}</td>
                        <td>{{number_format($income->amount)}}</td>
                        <td>{{$income->description}}</td>
                        <td>{{date('d-m-Y', strtotime($income->date)) ?? date('d-m-Y', strtotime($income->created_at)) ?? ''}}</td>
                        <td class="d-flex justify-content-end  align-items-center">
                            <a class="btn btn-sm btn-primary m-3" href="{{route('admin.income.show',[$income->id])}}"><i class="fa fa-info-circle"> {{__('text.word_view')}}</i></a> |
                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.income.edit',[$income->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash"> {{__('text.word_delete')}}</i></a>
                            <form id="delete" action="{{route('admin.income.destroy',$income->id)}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
@section('script')
<script>
    // $('#search').on('keyup', function() {
    //     val = $(this).val();
    //     url = "{{route('admin.get_searchUser')}}";
    //     // search_url = url.replace(':id', val);
    //     $.ajax({
    //         type: 'GET',
    //         data: {'name': val},
    //         url: url,
    //         success: function(response) {
    //             let html = new String();
    //             // console.log(response);
    //             let size = response.data.length;
    //             let data = response.data;
    //             for (i = 0; i < size; i++) {
    //                 html += '<tr>' +
    //                     '    <td>' + (i + 1) + '</td>' +
    //                     '    <td>' + data[i].name + '</td>' +
    //                     '    <td>' + data[i].email + '</td>' +
    //                     '    <td>' + data[i].gender + '</td>' +
    //                     '    <td>' + data[i].type + '</td>' +
    //                     '    <td class="d-flex justify-content-between align-items-center">' +
    //                     '        <a class="btn btn-xs btn-primary text-capitalize" href="' + data[i].link + '"> {{__("text.collect_income")}}</a>' +
    //                     '    </td>' +
    //                     '</tr>';
    //             }
    //             $('#content').html(html);

    //         },
    //         error: function(e) {
    //             console.log(e)
    //         }
    //     })
    // })
</script>
@endsection