@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <div class="content-panel">
            <div class="table-responsive">
                <table  border="0" class="table table-bordered" >
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Matricule</th>
                        @if(request('action') != 'class_list')
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Class</th>
                            <th></th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                        @include('admin.units.item',['item'=>$parent, 'm'=>0,])
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
{{--                    {{$students->links()}}--}}
                </div>
            </div>
        </div>
    </div>
@endsection
