@extends('admin.layout')

@section('section')
    <div class="w-100 py-3">
        <form action="{{Request::url()}}" method="get">
            @csrf
            <div class="d-flex justify-content-end flex-wrap">
                <div class="col-md-9">
                    <div class="py-2 form-group">
                        <label for="" class="text-secondary h6 fw-bold">{{__('text.select_academic_year')}}</label>
                        <select name="year" id="" class="form-control">
                            <option value="" selected>{{__('text.academic_year')}}</option>
                            @forelse(\App\Models\Batch::all() as $batch)
                                <option value="{{$batch->id}}">{{$batch->name}}</option>
                            @empty
                                <option value="" selected>{{__('text.academic_year_not_set')}} </option>
                            @endforelse
                        </select>
                    </div>
                    <div class="py-2 form-group">
                        <label for="" class="text-secondary h6 fw-bold">{{__('text.filter_statistics_by')}}</label>
                        <select name="filter_key" id="" class="form-control text-uppercase">
                            <option value="" selected>{{__('text.filter_by')}}</option>
                            <option value="class">{{__('text.word_class')}}</option>
                            <option value="program">{{__('text.word_program')}}</option>
                            <option value="level">{{__('text.word_level')}}</option>
                        </select>
                    </div>

                </div>
                <div class="d-flex flex-column justify-content-center">
                    <input type="submit" name="" id="" class=" btn btn-primary btn-sm" value="get statistics">
                </div>
            </div>
        </form>
        <div class="mt-5 pt-2">
            <div class="py-2 uppercase fw-bolder text-dark h4">
                <span>{{$title}} for </span>
                @if(request('year') != null)
                    <span>{{\App\Models\Batch::find(request('year'))->name}}</span>
                @else
                    <span>{{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}</span>
                @endif
            </div>
            @if(request()->has('filter_key'))
            <table class="table table-stripped">
                <thead class="bg-secondary text-black text-capitalize">
                    @php($count = 1)
                    <th>##</th>
                    <th>{{__('text.word_unit')}}</th>
                    <th>{{__('text.word_total')}}</th>
                    <th>{{__('text.word_males')}}</th>
                    <th>{{__('text.word_females')}}</th>
                </thead>
                <tbody>
                    @forelse($data ?? [] as $value)

                        <tr class="border-bottom border-dark" style="background-color: rgba(243, 243, 252, 0.4);">
                            <td class="border-left border-right">{{$count++}}</td>
                            <td class="border-left border-right">{{$value['unit']}}</td>
                            <td class="border-left border-right">{{$value['total']}}</td>
                            <td class="border-left border-right">{{$value['males']}}</td>
                            <td class="border-left border-right">{{$value['females']}}</td>
                        </tr>
                        
                    @empty
                        <tr class="border-bottom border-dark text-center">
                            {{__('text.phrase_6')}}
                        </tr>
                    @endforelse
                    @if(isset($data))
                        <tr class="border-top border-bottom" style="background-color: #fcfcfc;">
                            <td class="border-right border-left" colspan="2">{{__('text.word_total')}}</td>
                            <td class="border-right border-left">{{$data->sum('total')}}</td>
                            <td class="border-right border-left">{{$data->sum('males')}}</td>
                            <td class="border-right border-left">{{$data->sum('females')}}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            @endif
        </div>
    </div>
@endsection

@section('script')
@endsection
