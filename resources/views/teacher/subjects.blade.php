@extends('teacher.layout')
@section('section')
<!-- page start-->

<div class="col-sm-12">
    <p class="text-muted">
    <h4>My Subjects</h4>
    </p>

    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.credit_value')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @if(\request('class'))
                
                        @foreach($subjects as $k=>$subject)
                        <tr>
                            @php($class = \App\Models\ProgramLevel::find(request('class')))
                            <td>{{ $k+1 }}</td>
                            <td>{{ $subject->subject->name }}</td>
                            <td>{{ $subject->subject->coef }}</td>
                            <td>{{$class->program()->first()->name.': LEVEL '.$class->level()->first()->level}}</td>
                            <td>{{ \App\Models\Campus::find($subject->campus_id)->name ?? '----' }}</td>
                            <td style="float: right;">
                                <a class="btn btn-xs btn-primary" href="{{route('user.result', ['subject'=>$subject->id, 'class'=>request('class')])}}">Result</a>
                            </td>
                        </tr>
                        @endforeach
                    @else

                    @foreach($subjects as $k=>$subject)
                    <tr>
                        @php($class = \App\Models\ProgramLevel::find($subject->class_id))
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->subject->subject->name }}</td>
                        <td>{{ $subject->subject->subject->coef }}</td>
                        <td>{{$class->program()->first()->name.': LEVEL '.$class->level()->first()->level}}</td>
                        <td>{{ \App\Models\Campus::find($subject->campus_id)->name ?? '----' }}</td>
                        <td style="float: right;">
                            <a class="btn btn-xs btn-primary" href="{{route('user.result', [$subject->id])}}">Result</a>
                        </td>
                        <td style="float: right;">
                            <a class="btn btn-xs btn-success" href="{{route('user.subject.show', [$subject->class_id, $subject->id])}}">View More</a>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection