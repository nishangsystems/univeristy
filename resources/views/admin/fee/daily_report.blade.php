@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <form class="input-group input-group-merge border">
            <input type="date" id="password" name="date" class="w-100 border-0" value="{{request('date')}}">
            <button type="submit" class="border-0" class="text-capitalize">{{__('text.word_refresh')}}</button>
        </form>

        <div class="content-panel">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
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
                                <td>{{$fee->student->name}}</td>
                                <td>{{$fee->class->name}}</td>
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
