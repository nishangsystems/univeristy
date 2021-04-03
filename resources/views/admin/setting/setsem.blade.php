@extends('admin.layout')

@section('section')
    <div class="alert alert-info">
        <strong>Set Current Semseter</strong>
    </div>

    <form class="form-inline" action="{{route('admin.createsem')}}" method="post">
        @csrf
        <div class="form-group">
            <label class="control-label col-sm-2" for="pwd"></label>
            <div class="col-sm-10">
                <select class="form-control" id="sel1" name="sem" required>
                    <option ></option>
                    <?php $periods=\App\Models\Sequence::orderBy('id')->get();
                    foreach ($periods as $period) {

                    ?>
                    <option value="{{$period->id}}">{{$period->name}} </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <button type="submit" name="OK" class="btn btn-primary">Set as Sequence</button>
    </form>


    <table class="table table-bordered">
        <thead>
        <tr>
            <th>S/N</th>
            <th>Sequences</th>
        </tr>
        </thead>
        <tbody>
        <?php $rows= \App\Models\Sequence::orderBy('id','ASC')->get() ;
        $i=1;
        ?>
        @foreach ($rows as $row)
            <tr>
                <td>{{$i++}}</td>


                <td>{{$row->name}}</td>
                <td>

                    <a href="{{ route('admin.setugsem', $row->id)}}" class="btn btn-primary btn-xs">
                        Set as current sequencer</a>
                </td>
                @endforeach
            </tr>

        </tbody>
    </table>





@stop
