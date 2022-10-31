@extends('teacher.layout')
@section('section')
<div class="col-sm-12">
    <p class="text-muted">
    <h4 class="mb-4 text-capitalize">{{$class->program()->first()->name.' : LEVEL '.$class->level()->first()->level.' - '.\App\Models\Campus::find(request('campus'))->name.' '. __('text.word_students')}}</h4>
    </p>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $k=>$student)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$student->name}}</td>
                        <td>{{$student->matric}}</td>
                        <td> </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{-- $students->links() ?? '' --}}
            </div>
        </div>
    </div>
</div>
@endsection