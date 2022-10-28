@extends('admin.layout')

@section('section')
    <div class="w-100 py-3">
        <form action="{{route('admin.stats.unit-fees', request('class_id'))}}" method="get">
            @csrf
            <div class="form-group">
                <label for="" class="text-secondary h4 fw-bold">{{__('text.select_academic_year')}}</label>
                <div class="d-flex justify-content-between">
                    <select name="year" id="" class="form-control">
                        <option value="" selected>{{__('text.academic_year')}}</option>
                        @forelse(\App\Models\Batch::all() as $batch)
                            <option value="{{$batch->id}}">{{$batch->name}}</option>
                        @empty
                            <option value="" selected>{{__('text.academic_year_not_set')}} </option>
                        @endforelse
                    </select>
                    <input type="submit" name="" id="" value="get statistics">
                </div>
            </div>
        </form>
        <div class="mt-5 pt-2">
            <div class="py-2 uppercase fw-bolder text-dark h3">
                <span>{{$title}} for </span>
                @if(request('year') != null)
                    <span>{{\App\Models\Batch::find(request('year'))->name}}</span>
                @else
                    <span>{{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}</span>
                @endif
            </div>
            <table class="table table-stripped">
                <thead class="bg-secondary text-black text-capitalize">
                    @php($count = 1)
                    <th>##</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_paid')}}</th>
                    <th>{{__('text.word_owing')}}</th>
                </thead>
                <tbody>
                    @forelse($data ?? [] as $value)
                        <tr class="border-bottom border-dark">
                            <td class="border-left border-right">{{$count++}}</td>
                            <td class="border-left border-right">{{$value['name']}}</td>
                            <td class="border-left border-right">{{$value['paid']}}</td>
                            <td class="border-left border-right">{{$value['left']}}</td>
                        </tr>
                    @empty
                        <tr class="border-bottom border-dark text-center">
                            no statistics found for selected academic year
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')

@endsection
