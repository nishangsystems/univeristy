@extends('teacher.layout')
@section('section')
    <div class="py-5">
        <form class="form py-4 my-4" method="get">
            <div class="input-group-merge d-flex border rounded">
                <select class="form-control" name="campus">
                    <option value="">{{__('text.select_campus')}}</option>
                    @foreach(\App\Models\Campus::all() as $campus)
                        <option value="{{$campus->id}}">{{$campus->name}}</option>
                    @endforeach
                </select>
                <input type="submit" class="btn btn-xs btn-primary" value="{{__('text.word_get')}}">
            </div>
        </form>

        <table class="table">
            <thead class="text-primary">
                <th class="border-left border-right border-light">{{__('text.sn')}}</th>
                <th class="border-left border-right border-light">{{__('text.checked_in')}}</th>
                <th class="border-left border-right border-light">{{__('text.checked_out')}}</th>
                <th class="border-left border-right border-light">{{__('text.hours_covered')}}</th>
                <th class="border-left border-right border-light"></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach ($attendance as $record)
                    <tr class="border-bottom border-top border-light">
                        <td class="border-left border-right border-light">{{$k++}}</td>
                        <td class="border-left border-right border-light">{{date('d/m/Y', strtotime($record->check_in))}} <br> <b class="text-info">{{date('H:i', strtotime($record->check_in))}}</b> </td>
                        <td class="border-left border-right border-light">{{date('d/m/Y', strtotime($record->check_out))}} <br> <b class="text-info">{{date('H:i', strtotime($record->check_out))}}</b> </td>
                        <td class="border-left border-right border-light">{{\Illuminate\Support\Facades\Date::parse($record->check_in)->diffInHours(\Illuminate\Support\Facades\Date::parse($record->check_out))}}</b> </td>
                        <td class="border-left border-right border-light"></td>
                    </tr>
                @endforeach
                <tr class="border-bottom-2 border-top-2  border-primary text-primary text-center">
                    <td class="border-left border-right border-light text-uppercase" colspan="3"><b>{{__('text.word_total')}}</b></td>
                    <td class="border-left border-right border-light" colspan="2"><b>{{$total_hours}}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection