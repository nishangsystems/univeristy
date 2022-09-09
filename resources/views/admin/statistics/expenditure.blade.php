@extends('admin.layout')

@section('section')
    <div class="w-100 py-3">
        <form action="{{route('admin.stats.expenditure')}}" method="get">
            @csrf
            <div class="form-group">
                <div class="d-flex justify-content-between">
                    <div class="container">
                        <label for="" class="text-secondary h4 fw-bold text-capitalize">{{__('text.filter_by')}}:</label>
                        <select name="filter" id="stats_filter" class="form-control text-capitalize">
                            <option value="" selected>{{__('text.statistics_filter')}}</option>
                            <option value="month">{{__('text.word_month')}}</option>
                            <option value="year">{{__('text.word_year')}}</option>
                            <option value="range">{{__('text.word_range')}}</option>
                        </select>
                        <div class="py-3 mt-3 border-top" id="filterLoader">
                        </div>
                    </div>
                    <div class="">
                        <input type="submit" name="" id="" class="h-auto w-auto btn btn-light btn-md btn-primary" value="{{__('text.get_statistics')}}">
                    </div>
                </div>
            </div>
        </form>
        <div class="mt-5 pt-2">
            <div class="py-2 uppercase fw-bolder text-black h4">
                <span>{{$title}} for </span>
                <span>{{$filter ?? '----'}}</span>
            </div>
            <table class="table table-stripped">
                <thead class="bg-secondary text-black text-capitalize">
                    @php($count = 1)
                    <th>##</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_count')}}</th>
                    <th>{{__('text.word_amount')}}</th>
                </thead>
                <tbody>
                    @foreach($data ?? [] as $value)
                        <tr class="border-bottom border-dark">
                            <td class="border-left border-right">{{$count++}}</td>
                            <td class="border-left border-right">{{$value['name']}}</td>
                            <td class="border-left border-right">{{$value['count']}}</td>
                            <td class="border-left border-right">{{$value['cost']}}</td>
                        </tr>
                    
                    @endforeach
                    @if(isset($totals))
                    <tr class="text-black fw-bolder border-bottom border-dark fw-bolder fs-2" style="background-color: rgba(100,100,100,0.2);">
                        <td class="border-left border-right border-light"><span class="invisible">1000000</span></td>
                        <td class="border-left border-right border-light">{{$totals['name']}}</td>
                        <td class="border-left border-right border-light">{{$totals['count']}}</td>
                        <td class="border-left border-right border-light">{{$totals['cost']}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        
        document.querySelector('#stats_filter').addEventListener('change', function() {
            loadFilter(this)
        });

        function loadFilter(select){
            switch ($(select).val()) {
                case 'month':
                    html = `<div class="w-100">
                                <div class="form-group">
                                    <label for="" class="text-secondary h4 fw-bold">{{__("text.pick_a_month")}}:</label>
                                    <input type="month" name="value" class="form-control" placeholder="pick a month" id="">
                                </div>
                            </div>`;
                    $('#filterLoader').html(html);
                    break;
                case 'year':
                    html = `<div class="w-100">
                                <div class="form-group">
                                    <label for="" class="text-secondary h4 fw-bold">{{__("text.pick_a_year")}}:</label>
                                    <input type="number" min="2010" name="value" class="form-control" placeholder="pick a year" id="">
                                </div>
                            </div>`;
                    $('#filterLoader').html(html);
                    break;
                case 'range':
                    html = `<div class="w-100">
                                <div class="form-group">
                                    <label for="" class="text-secondary h4 fw-bold text-capitalize">{{__("text.word_from")}}:</label>
                                    <input type="date" name="start_date" class="form-control" placeholder="start date" id="">
                                </div>
                                <div class="form-group">
                                    <label for="" class="text-secondary h4 fw-bold text-capitalize">{{__("text.word_to")}}:</label>
                                    <input type="date" name="end_date" class="form-control" placeholder="end date" id="">
                                </div>
                            </div>`;
                    $('#filterLoader').html(html);
                    break;
            
                default:
                    break;
            }
        }
    </script>
@endsection
