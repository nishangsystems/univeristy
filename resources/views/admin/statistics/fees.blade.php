@extends('admin.layout')

@section('section')
    <div class="w-100 py-3">
        <form action="{{route('admin.stats.fees')}}" method="get">
            @csrf
            <div class="form-group">
                <label for="" class="text-secondary h4 fw-bold">select academic year</label>
                <div class="d-flex justify-content-between">
                    <select name="year" id="" class="form-control">
                        <option value="" selected>academic year</option>
                        @forelse(\App\Models\Batch::all() as $batch)
                            <option value="{{$batch->id}}">{{$batch->name}}</option>
                        @empty
                            <option value="" selected>academic year not set </option>
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
                <thead class="bg-secondary text-black">
                    @php($count = 1)
                    <th>##</th>
                    <th>Class</th>
                    <th>Number</th>
                    <th>Complete</th>
                    <th>Incomplete</th>
                    <th>Expected</th>
                    <th>Recieved</th>
                    <th>% Completed</th>
                    <th>% Uncompleted</th>
                    <th></th>
                </thead>
                <tbody>
                    @forelse($data as $value)
                        @if($value['target'])
                            <tr class="border-bottom border-dark" style="background-color: rgba(160, 160, 235, 0.3);">
                                <td class="border-left border-right">{{$count++}}</td>
                                <td class="border-left border-right">{{$value['class']}}</td>
                                <td class="border-left border-right">{{$value['students']}}</td>
                                <td class="border-left border-right">{{$value['complete']}}</td>
                                <td class="border-left border-right">{{$value['incomplete']}}</td>
                                <td class="border-left border-right">{{$value['expected']}}</td>
                                <td class="border-left border-right">{{$value['recieved']}}</td>
                                <td class="border-left border-right">{{$value['%complete']}}</td>
                                <td class="border-left border-right">{{$value['%incomplete']}}</td>
                                <td class="border-left border-right"><a href="{{route('admin.stats.unit-fees', $value['class_id'])}}">Details</a></td>
                            </tr>
                            @else
                            <tr class="border-bottom border-dark">
                                <td class="border-left border-right">{{$count++}}</td>
                                <td class="border-left border-right">{{$value['class']}}</td>
                                <td class="border-left border-right">{{$value['students']}}</td>
                                <td class="border-left border-right">{{$value['complete']}}</td>
                                <td class="border-left border-right">{{$value['incomplete']}}</td>
                                <td class="border-left border-right">{{$value['expected']}}</td>
                                <td class="border-left border-right">{{$value['recieved']}}</td>
                                <td class="border-left border-right">{{$value['%complete']}}</td>
                                <td class="border-left border-right">{{$value['%incomplete']}}</td>
                                <td class="border-left border-right"><a href="{{route('admin.stats.unit-fees', $value['class_id'])}}">Details</a></td>
                            </tr>
                            @endif
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
