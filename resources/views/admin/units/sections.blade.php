@extends('admin.layout')

@section('section')
    <div class="col-sm-12">
        <p class="text-muted">
            @if(!request('action'))
            <a href="{{route('admin.units.create', [$parent_id, '0'])}}" class="btn btn-info btn-xs">Add Sub Section</a>
                @endif
        </p>
        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" style = "padding: 20px; background: #ffffff; " id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($units as $unit)
                        <tr >
                            <td>{{ $unit->name }}</td>
                            <td>{{$unit->type->name}}</td>
                            <td class="d-flex justify-content-end align-items-center">
                                @if($unit->unit_id == 1)
                                    <a class="btn btn-xs btn-primary" href="{{route('admin.semesters.index', [$unit->id])}}">Semesters</a> | 
                                @endif
                                @if(request('action') == "class_list")
                                    <a class="btn btn-xs btn-success" href="{{route('admin.students.index', [$unit->id])}}">Students</a> |
                                    <a class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id])}}?action={{request('action')}}">Sub Units</a>
                                @else
                                    @if($unit->unit()->count() == 0)
                                        @if($unit->subjects()->count() == 0)
                                            <a class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id])}}?action={{request('action')}}">Sub Units</a> |
                                        @endif
                                    @else
                                        <a  class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id])}}?action={{request('action')}}">Sub Units</a> |
                                    @endif
                                    <a class="btn btn-xs btn-success" href="{{route('admin.units.edit',[$unit->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                                    <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                                    <form id="delete" action="{{route('admin.units.destroy',$unit->id)}}" method="POST" style="display: none;">
                                        @method('DELETE')
                                        {{ csrf_field() }}
                                    </form>
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
