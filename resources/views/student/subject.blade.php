@extends('student.layout')
@section('section')
@php
$student = Auth('student')->user();
@endphp
<div class="col-sm-12">
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" style="padding: 20px; background: #ffffff; " id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Coef</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if($student->class(\App\Helpers\Helpers::instance()->getYear()))
                    @foreach($student->class(\App\Helpers\Helpers::instance()->getYear())->subjects as $k=>$subject)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{ $subject->coef }}</td>

                        <td class="justify-end">
                            <a class="btn btn-xs btn-primary" href="{{route('student.subject.notes', [$subject->id])}}">View Notes</a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <p>Your profile cant be matched to any class in <b>{{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name}}</b> </p>
                    </div>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop