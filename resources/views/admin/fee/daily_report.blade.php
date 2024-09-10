@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <div class="container-fluid">
            <form class="row mb-5">
                <div class="col-10">
                    <input type="date" name="date" class="form-control" value="{{request('date')}}">
                </div>
                <div class="col-2 d-flex justify-content-end">
                    <span>
                        <button type="submit" class="btn btn-xs btn-primary rounded" class="text-capitalize">{{__('text.word_refresh')}}</button>
                    </span>
                </div>
            </form>
        </div>

        <div class="content-panel">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th>{{__('text.word_amount')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="content">
                    @php($total = 0)
                    @foreach($fees as $k=>$fee)
                        <tr>
                                <td>{{$k+1}}</td>
                                <td>{{$fee->student->matric ?? ''}}</td>
                                <td>{{$fee->student->name ?? ''}}</td>
                                <td>{{$fee->student->classes()->first()->class->program->name .' : Level '.$fee->student->classes()->first()->class->level->level}}</td>
                                <td>{{$fee->amount}} XAF</td>
                                <td>
                                    <a onclick="event.preventDefault();
                                        document.getElementById('delete{{$fee->id}}').submit();" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
                                    <form id="delete{{$fee->id}}" action="{{route('admin.fee.destroy',$fee->id)}}" method="POST" style="display: none;">
                                        @method('DELETE')
                                        {{ csrf_field() }}
                                    </form>
                                </td>
                            </tr>
                            @php($total += $fee->amount)
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
