@extends('admin.layout')

@section('section')
<!-- page start-->

<div class="col-sm-12">
    <p class="text-muted">
        <a href="{{route('admin.units.subjects.manage_class_subjects', $parent->id)}}" class="btn btn-info btn-xs text-capitalize">Manage Subjects</a>
    </p>

    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Coefficient</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($subjects as $k=>$subject)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{ $subject->coef }}</td>
                        <td class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-primary" href="{{route('admin.edit.class_subjects',[$subject->class_id,$subject->subject_id])}}">
                                <i class="fa fa-edit"> Edit</i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{$subjects->links()}}
            </div>
        </div>
    </div>
</div>
@endsection