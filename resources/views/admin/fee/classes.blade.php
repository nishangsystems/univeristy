@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Fee</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($classes as $k=>$class)
                        @php($fee = $class->fee())
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $class->name }}</td>
                            {{-- <td>{{ $class->parent?$class->parent->name:'' }}</td> --}}
                            <td>{{$class}}</td>
                            <td>{{ $fee > 0 ? $fee." FCFA":'NOT SET' }} </td>
                            <td>
                               @if($class->unit->count() > 0)
                                    <a href="{{route('admin.fee.classes')}}?parent_id={{$class->id}}" class="btn btn-sm btn-warning">Sub classes</a>
                               @else
                                    <a href="{{route('admin.fee.student', $class->id)}}" class="btn btn-sm btn-secondary">Students</a>
                                @endif
                                <a href="{{route('admin.fee.list.index', $class->id)}}" class="btn btn-sm btn-primary">Fee Listing</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
