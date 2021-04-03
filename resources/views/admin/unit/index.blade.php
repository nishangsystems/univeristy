@extends('layout.base')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <p class="text-muted">
            @if(\App\SchoolUnit::find($parent_id)->unit()->count() > 0)
                <a href="{{route('admin.units.create', [$parent_id, '0'])}}" class="btn btn-info btn-xs">Add {{\App\SchoolUnit::find($parent_id)->unit()->first()->unitType->name}}</a>
            @elseif(\App\SchoolUnit::find($parent_id)->unit()->count() == 0 && \App\SchoolUnit::find($parent_id)->subjects()->count() == 0)
                <a href="{{route('admin.units.create', [$parent_id, '0'])}}" class="btn btn-info btn-xs">Add Units</a> |
                <a href="{{route('admin.subjects.index', [$parent_id,\Config::get('config.school')])}}" class="btn btn-info btn-xs">Subjects</a>
            @else
                <a href="{{route('admin.subjects.index', [$parent_id,\Config::get('config.school')])}}" class="btn btn-info btn-xs">Subjects</a>
            @endif

        </p>
        <div class="content-panel">
            <div class="adv-table">
                <table cellpadding="0" cellspacing="0" border="0" class="table" style = "padding: 20px; background: #ffffff; " id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($units as $unit)
                        <tr>
                            <td>{{ $unit->byLocale(app()->getLocale())->name }}</td>
                            <td style="float: right;">
                                @if($unit->unit()->count() == 0)
                                    @if($unit->subjects()->count() == 0)
                                        <a class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id,'0'])}}">Sub Units</a> |
                                    @endif
                                    <a href="{{route('admin.subjects.index', [$unit->id,'0'])}}" class="btn btn-info btn-xs">Subjects</a> |
                                @else
                                    <a  class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id,'0'])}}">Sub Unit</a> |
                                @endif
                                <a class="btn btn-xs btn-success" href="{{route('admin.units.edit',[$unit->id,$flag])}}"><i class="fa fa-edit"> Edit</i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
