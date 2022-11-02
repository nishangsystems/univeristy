
@foreach($item->students(\Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->get() as $k=>$student)
    <tr>
        <td>{{$m + $k}}</td>
        <td>{{$student->name}}</td>
        <td>{{$student->matric}}</td>
        @if(request('action') != 'class_list')
            <td>{{$student->email}}</td>
            <td>{{$student->phone}}</td>
            <td>{{$student->address}}</td>
            <td>{{$student->class(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}</td>
            <td class="d-flex justify-content-end align-items-center " >
                <a class="btn btn-xs btn-primary" href="{{route('admin.student.show',[$student->id])}}"><i class="fa fa-eye"> Profile</i></a>
                @if(request('action') != 'class_list')
                    | <a class="btn btn-xs btn-success" href="{{route('admin.student.edit',[$student->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                    <a onclick="event.preventDefault();
                                                    document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">Delete</a>
                    <form id="delete" action="{{route('admin.student.destroy',$student->id)}}" method="POST" style="display: none;">
                        @method('DELETE')
                        {{ csrf_field() }}
                    </form>
                @endif
            </td>
        @endif
    </tr>
    @php $m += 1; @endphp
@endforeach
@foreach ($item->unit as $unit)
    @include('admin.units.item', ['item' => $unit, '$m' => ])
@endforeach
