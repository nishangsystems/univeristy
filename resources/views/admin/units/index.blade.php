@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <p class="text-muted">
           @if(!request('action'))
                @if(\App\Models\SchoolUnits::find($parent_id)->unit->count() > 0)
                    <a href="{{route('admin.units.create', [$parent_id])}}?type={{\App\Models\SchoolUnits::find($parent_id)->unit->first()->type->id}}" class="btn btn-info btn-xs">Add {{\App\Models\SchoolUnits::find($parent_id)->unit->first()->type->name}} </a>
                @elseif(\App\Models\SchoolUnits::find($parent_id)->subjects->count() > 0)
                    <a href="{{route('admin.subjects.index', [$parent_id])}}" class="btn btn-info btn-xs">Add Subjects</a>
                @else
                    <a href="{{route('admin.units.create', [$parent_id])}}" class="btn btn-info btn-xs">Add Unit</a> |
                    <a href="{{route('admin.subjects.index', [$parent_id])}}" class="btn btn-info btn-xs">Add Subjects</a>
                @endif
           @endif
        </p>

        <div class="content-panel">
            <div class="table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($units as $unit)
                        <tr>
                            <td>{{ $unit->name }}</td>
                            <td>{{$unit->type->name}}</td>
                            <td class="d-flex justify-content-end align-items-center">
                                @if(request('action') == 'class_list')
                                    <a class="btn btn-xs btn-primary" href="{{route('admin.students.index', [$unit->id])}}?action={{request('action')}}">Students</a> |
                                    <a  class="btn btn-xs btn-success" href="{{route('admin.units.index', [$unit->id])}}?action={{request('action')}}">Sub Unit</a>
                                @else
                                    @if($unit->students(Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->count()  > 0)
                                        <a class="btn btn-xs btn-primary" href="{{route('admin.students.index', [$unit->id])}}">Students</a> |
                                    @endif

                                    @if($unit->unit()->count() == 0)
                                        @if($unit->subjects()->count() == 0)
                                            <a class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id])}}">Sub Units</a> |
                                        @endif
                                        <a href="{{route('admin.units.subjects', [$unit->id])}}" class="btn btn-info btn-xs">Subjects</a> |
                                    @else
                                        <a  class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id])}}">Sub Unit</a> |
                                    @endif
                                    <a class="btn btn-xs btn-success" href="{{route('admin.units.edit',[$unit->id])}}"><i class="fa fa-edit"> Edit</i></a>
                                    @if($unit->unit->count() == 0)
                                        | <a onclick="event.preventDefault();
                                        document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                                        <form id="delete" action="{{route('admin.units.destroy',$unit->id)}}" method="POST" style="display: none;">
                                            @method('DELETE')
                                            {{ csrf_field() }}
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
