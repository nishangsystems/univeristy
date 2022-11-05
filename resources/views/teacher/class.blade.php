@extends('teacher.layout')
@section('section')
    <div class="col-sm-12">
        <p class="text-muted text-capitalize">
           {{__('text.my_classes')}}
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                        <tr class="text-capitalize">
                            <th>{{__('text.sn')}}</th>
                            <th>{{__('text.word_class')}}</th>
                            <th>{{__('text.word_campus')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                    @php($k = 1)
                    @foreach($units as $unit)

                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{ $unit->program()->first()->name.' : LEVEL '.$unit->level()->first()->level}}</td>
                            <td>{{\App\Models\Campus::find($unit->campus_id)->name}}</td>
                            <td style="float: right;">
                                <a class="btn btn-xs btn-success" href="{{route('notifications.index', ['C', $unit->id, $unit->campus_id])}}">{{__('text.word_notifications')}}</a>
                                <a class="btn btn-xs btn-primary" href="{{route('user.class.student', [$unit->id])}}?campus={{$unit->campus_id}}">Students</a>
                                <a class="btn btn-xs btn-success" href="{{route('user.subject')}}?class={{$unit->id}}&campus={{$unit->campus_id}}">Subjects</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
