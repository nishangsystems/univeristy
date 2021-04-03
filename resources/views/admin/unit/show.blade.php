@extends('layout.base')

@section('section')
    <div class="col-sm-12">
        <p id="add-unit" class="text-muted">
            <a href="{{route('admin.schools.create')}}" class="btn btn-info btn-xs">Create <b>UNIT</b></a>
        </p>

        <p id="new-unit" id="text-muted">
            <button onclick="newUnit('1')" class="btn btn-success btn-xs">Add new <b>UNIT</b></button>
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

                    <tr>
                        <td>1</td>
                        <td style="float: right;">
                            <a  class="btn btn-xs btn-primary" href=""><i class="fa fa-eye"></i></a> | <a class="btn btn-xs btn-success" href=""><i class="fa fa-edit"></i></a>
                        </td>
                    </tr>

                    <tr>
                        <td>1</td>
                        <td style="float: right;">
                            <a  class="btn btn-xs btn-primary" href=""><i class="fa fa-eye"></i></a> | <a class="btn btn-xs btn-success" href=""><i class="fa fa-edit"></i></a>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- page end-->
@stop
