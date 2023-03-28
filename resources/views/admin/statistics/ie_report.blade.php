@extends('admin.layout')
@section('section')
<div class="py-3">
    <div id="section">
        <div class="form-group">
            <div>
                <label class="border-0 h5 mx-3">{{__('text.select_month')}}</label>
                <div class="input-group input-group-merge border">
                    <input class="w-100 border-0 section form-control" type="month" id="month" required placeholder="select month">
                    <select class="w-100 border-0 section form-control" id="campus">
                        <option value="">{{__('text.select_campus')}}</option>
                        @foreach (\App\Models\Campus::orderBy('name')->get() as $campus)
                            <option value="{{$campus->id}}" {{request('campus') == $campus->id ? 'selected' : ''}}>{{$campus->name}}</option>
                        @endforeach
                    </select>
                    <button type="submit" onclick="getReport()" class="border-0 text-uppercase" >{{__('text.word_get')}}</button>
                </div>
            </div>
        </div>
    </div>

    <h3 class="text-center fw-bolder my-2" id="table_title"></h3>
    <table class="table">
        <thead class="text-capitalize">
            <th>S/N</th>
            <th>{{__('text.word_date')}}</th>
            <th>{{__('text.word_income')}}</th>
            <th>{{__('text.word_expenditure')}}</th>
            <th>{{__('text.word_total')}}</th>
        </thead>
        <tbody id="table_data">

        </tbody>
    </table>
</div>
@endsection
@section('script')
<script>
    function getReport() {
        let month = $('#month').val();
        url = "{{route('admin.stats.ie.report')}}";
        $.ajax({
            method: 'get',
            url: url,
            data: {'month': month},
            success: function(resp){
                console.log(resp);
                let html = '';
                let i = 1;
                for (const key in resp.report) {
                    html += `<tr>
                        <td class="border-left border-right">`+ i++ +`</td>
                        <td class="border-left border-right">`+ resp.report[key].date +`</td> 
                        <td class="border-left border-right">`+ resp.report[key].income +`</td> 
                        <td class="border-left border-right">`+ resp.report[key].expenditure +`</td> 
                        <td class="border-left border-right">`+ resp.report[key].balance +`</td> `
                }
                html += `
                    <tr class="border-top border-bottom bg-light text-capitalize ">
                        <td class="border-left border-right" colspan="2">{{__('text.word_total')}}</td>
                        <td class="border-left border-right">`+resp.totals.income+`</td>
                        <td class="border-left border-right">`+resp.totals.expenditure+`</td>
                        <td class="border-left border-right">`+resp.totals.balance+`</td>
                    </tr>
                    `;
                $('#table_data').html(html);
                $('#title').text(resp.title);
            }

        })
    }
</script>
@endsection