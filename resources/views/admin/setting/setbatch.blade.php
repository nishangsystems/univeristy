@extends('admin.layout')

@section('section')

<table class="table table-bordered">
    <thead>
        <tr class="text-capitalize">
            <th>{{__('text.sn')}}</th>
            <th>{{__('text.word_year')}}</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php $rows = \App\Models\Batch::orderBy('id', 'ASC')->get();
        $i = 1;
        ?>
        @foreach ($rows as $row)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$row->name}}</td>

            <td>
                <form method="POST" action="{{ route('admin.createacademicyear', $row->id)}}" role="form">
                    <button type="submit" class="btn btn-primary btn-xs text-capitalize">
                        {{__('text.set_academic_year')}}
                    </button>
                    @csrf
                </form>
            </td>
            <td>
                <a href="{{ route('admin.deletebatch', $row->id)}}" class="btn btn-danger btn-xs">
                    Delete
                </a>
            </td>
            @endforeach
        </tr>

    </tbody>
</table>
</div>

</body>

<!-- Mirrored from www.w3schools.com/bootstrap/tryit.asp?filename=trybs_table_bordered&stacked=h by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 13 Mar 2016 11:04:54 GMT -->

</html>

@stop