@extends('admin.layout')

@section('section')

    <style>
        input{
            width: 80%;
        }
    </style>


    <div class="alert alert-info">
        <strong>Set Current Academic Year</strong>
    </div>

    <form class="form-inline" action="{{route('admin.createacademicyear')}}" method="post">
        @csrf
        <div class="d-flex w-100 flex-nowrap justify-content-between align-items-center  my-4">
            <div class="form-group ">
                <label for="inputEmail4">Start Year</label>
                <select class="form-control" id="sel1" name="start"  required>

                    <option></option>
                    <?php for($x=2019; $x<=2030; $x++){ ?>
                    <option value="{{$x}}">{{$x}}</option>
                    <?php } ?>
                </select>
            </div>


            <div class="form-group">
                <label for="inputPassword4">End Year</label>
                <select class="form-control" id="sel1" name="end" required>

                    <option></option>
                    <?php for($x=2019; $x<=2030; $x++){ ?>
                    <option value="{{$x}}">{{$x}}</option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" name="OK" class="btn btn-primary">Set as Academic Year</button>
        </div>

    </form>


    <table class="table table-bordered">
        <thead>
        <tr>
            <th>S/N</th>
            <th>Year</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $rows= \App\Models\Batch::orderBy('id','DESC')->get() ;
        $i=1;
        ?>
        @foreach ($rows as $row)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$row->name}}</td>
                <td><a href="{{ route('admin.deletebatch', $row->id)}}" class="btn btn-danger btn-xs">
                        Delete</a></td>
                @endforeach
            </tr>

        </tbody>
    </table>
    </div>

    </body>

    <!-- Mirrored from www.w3schools.com/bootstrap/tryit.asp?filename=trybs_table_bordered&stacked=h by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 13 Mar 2016 11:04:54 GMT -->
    </html>

@stop
