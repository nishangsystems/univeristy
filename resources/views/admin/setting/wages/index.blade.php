@extends('admin.layout')
@section('section')
    <div class="py-4">
        <div class="d-flex justify-content-end py-2">
            <a href="{{route('admin.users.wages.create')}}" class="btn btn-sm btn-primary">{{__('text.add_wages')}}</a>
        </div>
        <div class="py-2">
            <input class="form-control" id="search_teacher" placeholder="search teacher by name, matricule or username">
            <div class="py-2">
                <table>
                    <thead class="text-capitalize">
                        <th class="border">##</th>
                        <th class="border">{{__('text.word_background')}}</th>
                        <th class="border">{{__('text.word_teacher')}}</th>
                        <th class="border">{{__('text.word_level')}}</th>
                        <th class="border">{{__('text.word_rate')}}</th>
                        <th class="border"></th>
                    </thead>
                    <tbody>
                        @php($k = 1)
                        @foreach ($rates as $rate)
                            <tr>
                                <td>{{$k++}}</td>
                                <td>{{$rate->background->background_name}}</td>
                                <td>{{$rate->teacher->name??null}}</td>
                                <td>{{$rate->level->level??null}}</td>
                                <td>{{$rate->price}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection