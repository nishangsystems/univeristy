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
                    <th>{{__('text.word_students')}}</th>
                    <th>#{{__('text.word_complete')}}</th>
                    <th>#{{__('text.word_incomplete')}}</th>
                    <th>{{__('text.word_expected')}}</th>
                    <th>{{__('text.word_recieved')}}</th>
                    <th>{{__('text.percentage_complete')}}</th>
                    <th>{{__('text.percentage_uncompleted')}}</th>
                    <th>{{__('text.percentage_recieved')}}</th>
                </thead>
                <tbody>
                    @forelse($data ?? [] as $value)

                        <tr class="border-bottom border-dark" style="background-color: rgba(242, 242, 250, 0.4);">
                            <td class="border-left border-right">{{$count++}}</td>
                            <td class="border-left border-right">{{$value['unit']}}</td>
                            <td class="border-left border-right">{{$value['students']}}</td>
                            <td class="border-left border-right">{{$value['complete']}}</td>
                            <td class="border-left border-right">{{$value['incomplete']}}</td>
                            <td class="border-left border-right">{{number_format($value['expected'])}}</td>
                            <td class="border-left border-right">{{number_format($value['recieved'])}}</td>
                            <td class="border-left border-right">{{number_format($value['per_completed'], 2)}}</td>
                            <td class="border-left border-right">{{number_format($value['per_uncompleted'], 2)}}</td>
                            <td class="border-left border-right">{{number_format($value['per_recieved'], 2)}}</td>
                        </tr>
                        
                    @empty
                        <tr class="border-bottom border-dark text-center">
                            {{__('text.phrase_6')}}
                        </tr>
                    @endforelse
                    @if(isset($data))
                    <tr class="border-bottom border-top border-primary" style="background-color: rgb(225, 245, 255);">
                        <td class="border-left border-right" colspan="2">{{__('text.word_total')}}</td>
                        <td class="border-left border-right">{{$data->sum('students')}}</td>
                        <td class="border-left border-right">{{$data->sum('complete')}}</td>
                        <td class="border-left border-right">{{$data->sum('incomplete')}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('expected'), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('recieved'), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('per_completed')/$data->count(), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('per_uncompleted')/$data->count(), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('per_recieved')/$data->count(), 2)}}</td>
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
