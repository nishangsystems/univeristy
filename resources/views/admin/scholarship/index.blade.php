@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div>
        @include("admin.scholarship.create")

    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Amount (CFA)</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scholarships as $k=>$scholarship)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$scholarship->name}}</td>
                        <td>{{number_format($scholarship->amount)}}</td>
                        <td>{{$scholarship->type}}</td>
                        @if($scholarship->status ==1)
                        <td>Active</td>
                        @else
                        <td>Inactive</td>
                        @endif
                        <td class="d-flex justify-content-end  align-items-center">
                            <a class="btn btn-sm btn-primary m-3" href="{{route('admin.scholarship.show',[$scholarship->id])}}"><i class="fa fa-info-circle"> View</i></a> |
                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.scholarship.edit',[$scholarship->id])}}"><i class="fa fa-edit"> Edit</i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{$scholarships->links()}}
            </div>
        </div>
    </div>
</div>
@endsection