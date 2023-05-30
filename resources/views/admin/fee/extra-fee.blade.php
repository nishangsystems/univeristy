@extends('admin.layout')

@section('section')
<div class="col-sm-12">

    @if(request('student_id') == null)
        <div class="my-3">
            <input class="form-control" id="search" placeholder="Search Student by Name or Matricule" required name="student_id" />
        </div>

        <div class="content-panel">
            <div class="table-responsive">
                <table class="table-bordered">
                    <thead>
                        <tr class="text-capitalize">
                            <th>#</th>
                            <th>{{__('text.word_name')}}</th>
                            <th>{{__('text.word_matricule')}}</th>
                            <th>{{__('text.word_program')}}</th>
                            <th>{{__('text.word_gender')}}</th>
                            <th>{{__('text.word_campus')}}</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody id="content">

                    </tbody>
                </table>

            </div>
        </div>
    @else
        <div class="py-3">
            <form action="{{Request::url()}}/save" method="get">
                <div class="my-2 row">
                    <span class="text-capitalize col-md-2">{{__('text.word_amount')}}</span>
                    <div class="col-md-10">
                        <input type="number" name="amount" id="" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="my-2 row">
                    <span class="text-capitalize col-md-2">{{__('text.word_year')}}</span>
                    <div class="col-md-10">
                        <select name="year_id" id="" class="form-control" required>
                            <option value=""></option>
                            @foreach(\App\Models\Batch::all() as $batch)
                                <option value="{{$batch->id}}">{{$batch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-between text-capitalize">
                    <a href="{{route('admin.home')}}" class="btn btn-sm btn-danger">&lArr; {{__('text.word_back')}}</a>
                    <input type="submit" class="btn btn-sm btn-primary" name="" id="" value="{{__('text.word_save')}}">
                </div>
            </form>
        </div>
        <div class="py-3">
            <table class="table">
                <thead class="text-capitalize bg-light">
                    <th>##</th>
                    <th>{{__('text.word_amount')}}</th>
                    <th>{{__('text.word_date')}}</th>
                    <th>{{__('text.word_batch')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\ExtraFee::where('student_id', request('student_id'))->get() as $ext)
                        <tr class="border-bottom">
                            <td>{{$k++}}</td>
                            <td>{{$ext->amount}}</td>
                            <td>{{date('l d-m-Y', strtotime($ext->created_at))}}</td>
                            <td>{{$ext->batch->name}}</td>
                            <td><a class="btn btn-xs btn-danger" onclick="
                                confirm('Are you sure you want to delete this extra-fee entry. You will not be able to undo this operation.') ? window.location = `{{route('admin.extra-fee.destroy', [request('student_id'), $ext->id])}}` : null;
                            ">{{__('text.word_delete')}}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
@section('script')
<script>
    $('#search').on('keyup', function() {
        val = $(this).val();
        url = "{{route('admin.get_searchStudent')}}";
        search_url = url.replace(':id', val);
        $.ajax({
            type: 'GET',
            url: search_url,
            data: {'name': val},
            success: function(response) {
                let html = new String();
                let size = response.data.length;
                let data = response.data;
                for (i = 0; i < size; i++) {
                    html += '<tr>' +
                        '    <td>' + (i + 1) + '</td>' +
                        '    <td>' + data[i].name + '</td>' +
                        '    <td>' + data[i].matric + '</td>' +
                        '    <td>' + data[i].class + '</td>' +
                        '    <td>' + data[i].gender + '</td>' +
                        '    <td>' + data[i].campus + '</td>' +
                        '    <td class="d-flex justify-content-between align-items-center">' +
                        '        <a class="btn btn-xs btn-primary text-capitalize" href="{{Request::url()}}/'+ data[i].id +'">{{__("text.add_fee")}}</a>' +
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