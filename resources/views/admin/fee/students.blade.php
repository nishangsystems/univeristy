@extends('admin.layout')
@section('section')
    <div class="col-sm-12">
        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $k=>$student)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$student->name}}</td>
                            <td>{{$student->phone}}</td>
                            <td>{{$student->total()}} FCFA </td>
                            <td>{{$student->paid()}} FCFA </td>
                            <td>{{$student->bal()}} FCFA </td>
                            <td class="d-flex justify-content-between align-items-center">
                                <a class="btn btn-xs btn-primary" href="{{route('admin.fee.student.payments.index',[$student->id])}}"> Fee Collections</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{$students->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection
