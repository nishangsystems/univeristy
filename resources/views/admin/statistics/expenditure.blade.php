@extends('admin.layout')

@section('section')
    @php
        $year_listing = \App\Helpers\Helpers::instance()->year_listing();
    @endphp
    <div class="w-100 py-3">
        <form action="{{route('admin.stats.expenditure')}}" method="get">
            <div class="form-group">
                <div class="d-flex flex-wrap justify-content-between">
                    <div class="">
                        <label for="" class="text-secondary h4 fw-bold text-capitalize">{{__('text.select_campus')}}:</label>
                        <select name="campus" class="form-control">
                            <option value="">{{__('text.word_all')}}</option>
                            @foreach (\App\Models\Campus::orderBy('name')->get() as $campus)
                                <option value="{{$campus->id}}" {{request('campus') == $campus->id ? 'selected' : ''}}>{{$campus->name??null}}</option>                            
                            @endforeach
                        </select>
                    </div>
                    <div class="">
                        <label for="" class="text-secondary h4 fw-bold text-capitalize">{{__('text.filter_by')}}:</label>
                        <select name="filter" id="stats_filter" class="form-control">
                            <option value="">{{__('text.statistics_filter')}}</option>
                            <option value="month" {{request('filter') == 'month' ? 'selected' : ''}}>{{__('text.word_month')}}</option>
                            <option value="year" {{request('filter') == 'year' ? 'selected' : ''}}>{{__('text.word_year')}}</option>
                            <option value="range" {{request('filter') == 'range' ? 'selected' : ''}}>{{__('text.word_range')}}</option>
                        </select>
                    </div>
                    <div class="py-3 mt-3 border-top" id="filterLoader">
                    </div>
                    <div class="">
                        <input type="submit" name="" id="" class="h-auto w-auto btn btn-primary btn-md" value="get statistics">
                    </div>
                </div>
            </div>
        </form>
        <div class="mt-5 pt-2">
            <div class="py-2 uppercase fw-bolder text-black h4">
                <span>{{$title}} for {{$filter ?? '----'}}</span>
            </div>
            <table class="table table-stripped">
                <thead class="bg-secondary text-black text-capitalize">
                    @php($count = 1)
                    <th>##</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_date')}}</th>
                    <th>{{__('text.word_amount')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach($data ?? [] as $value)
                        <tr class="border-bottom border-dark">
                            <td class="border-left border-right">{{$count++}}</td>
                            <td class="border-left border-right">{{$value->name}}</td>
                            <td class="border-left border-right">{{date('l d-m-Y', strtotime($value->date))}}</td>
                            <td class="border-left border-right">{{number_format($value->amount_spend)}}</td>
                        </tr>
                    
                    @endforeach
                    @if(isset($totals))
                        <tr class="text-black fw-bolder border-bottom border-dark fw-bolder fs-2" style="background-color: rgba(200,200,200,0.2);">
                            <td class="border-left border-right border-light" colspan="3">{{$totals['name']}}</td>
                            <td class="border-left border-right border-light">{{number_format($totals['cost'])}}</td>
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
                                    <select name="value" class="form-control">
                                        @foreach($year_listing as $yr)
                                            <option value="{{$yr}}">{{$yr}}</option>
                                        @endforeach
                                    </select>
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
