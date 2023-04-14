@extends('admin.layout')
@section('section')
    <div class="py-4">
        
        <div class="py-3">
            <form class=" roundede py-4 px-3" method="post">
                @csrf
                
                <div class="py-2">
                    <label class="text-capitalize h4">{{__('text.select_level')}}</label>
                    <select class="form-control" name="level_id" required>
                        <option value=""></option>
                        @foreach (\App\Models\Level::all() as $lvl)
                            <option value="{{$lvl->id}}">{{$lvl->level}}</option>
                        @endforeach
                    </select>
                <div class="py-2">
                    <label class="text-capitalize h4">{{__('text.hourly_rate')}}</label>
                    <input type="number" class="form-control" name="rate" required>
                </div>
                <div class="d-flex justify-content-end py-2">
                    <input type="submit" class="btn btn-sm btn-primary" value="{{__('text.word_save')}}">
                </div>
            </form>
        </div>
        <div class="py-4">
            <table>
                <thead class="text-capitalize bg-light text-primary">
                    <th class="border">{{__('text.sn')}}</th>
                    <th class="border">{{__('text.word_level')}}</th>
                    <th class="border">{{__('text.word_rate')}}</th>
                    <th class="border"></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach ($rates as $record)
                        <tr>
                            <td class="border">{{$k++}}</td>
                            <td class="border">{{$record->level->level}}</td>
                            <td class="border">{{$record->price}}</td>
                            <td class="border">
                                <a class="btn btn-sm btn-danger" href="{{route('admin.users.wages.drop', ['teacher_id'=>request('teacher_id'), 'wage_id'=>$record->id])}}">{{__('text.word_drop')}}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
