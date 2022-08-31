@extends('teacher.layout')
@section('section')
<div class="container-fluid h-screen d-flex flex-column justify-content-center">
    <div class="w-100 d-block py-3  rounded-lg bg-light">
            <h2 class="my-3 text-dark fw-bolder text-center w-100">{{$title}}</h2>
            <div class="w-100 py-2 d-md-flex">
                <div class="w-50 px-4">
                    <h3 class="py-1 fw-bold text-dark">Academic Year</h3>
                    <div class="form-group w-100 py-1">
                        <label for="year_from" class="text-secondary">From:</label>
                        <span id="" class="form-control text-dark rounded">{{\App\Models\Batch::find($request->year_from)->name}}</span>
                    </div>
                    <div class="form-group w-100 py-1">
                        <label for="year_to" class="text-secondary">To:</label>
                        <span id="" class="form-control text-dark rounded">{{\App\Models\Batch::find($request->year_to)->name}}</span>
                    </div>
                </div>
                <div class="w-50 px-4">
                    <h3 class="py-1 fw-bold text-dark">Class</h3>
                    <div class="form-group w-100 py-1">
                        <label for="class_from" class="text-secondary">From:</label>
                        <span id="" class="form-control text-dark rounded">{{$classes['cf']['name']}}</span>
                    </div>
                    <div class="form-group w-100 py-1">
                        <label for="class_to" class="text-secondary">To:</label>
                        <span id="" class="form-control text-dark rounded">{{$classes['ct']['name']}}</span>
                    </div>
                </div>
            </div>
    </div>
    <form action="{{route('user.students.promote')}}" method="post" class="w-100">
        @csrf
        <input type="hidden" name="class_from" value="{{request('class_from')}}">
        <input type="hidden" name="class_to" value="{{request('class_to')}}">
        <input type="hidden" name="year_from" value="{{request('year_from')}}">
        <input type="hidden" name="year_to" value="{{request('year_to')}}">
        @if(request('promotion_batch') != '') <input type="hidden" name="type" value="demotion" id="">
        @else <input type="hidden" name="type" value="promotion" id="">
        @endif
        <div class="w-100 py-2">
            <div class="d-flex justify-content-center align-moddle">
                <div class="mx-4">
                    <label for="total" class="text-secondary">Total:</label>
                    <span class="text-dark fw-bold fs-3" id="student-total">{{count($students)}}</span>
                </div>
                <div class="mx-4">
                    <label for="total" class="text-secondary">Selected:</label>
                    <span class="text-dark fw-bold fs-3" id="selected">-</span>
                </div>
            </div>
            <div class="w-100">
                
                <table class="w-100 table-striped">
                    <thead class="w-100 bg-secondary text-light">
                        <th class="border-left border-right"><input type="checkbox" value="1" id="checkall" onchange="updateSelection(event)"><span>all</span></th>
                        <th class="border-left border-right">S/N</th>
                        <!-- <th class="border-left border-right">Matric</th> -->
                        <th class="border-left border-right">Name</th>
                        <th class="border-left border-right">Email</th>
                        <th class="border-left border-right">Average</th>
                        @php $sn = 0; @endphp
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr class="fw-bolder fs-3 text-dark border-bottom">
                            <td class="border-left border-right"><input type="checkbox" class="checker" name="students[]" value="{{$student->id}}" id="" onchange="updateSelected()"></td>
                            <td class="border-left border-right">{{++$sn}}</td>
                            <!-- <td class="border-left border-right"><span>{{$student->matric}}</span></td> -->
                            <td class="border-left border-right"><span>{{$student->name}}</span></td>
                            <td class="border-left border-right"><span>{{$student->email}}</span></td>
                            <td class="border-left border-right"><span onload="getAverage(this, '{{$student->id}}')">student av</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary mx-4 my-2">promote</button>
        </div>
    </form>
</div>
@endsection
@section('script')
<script>

    function updateSelected(){
        $('#selected').text($('.checker:checked').length);
    }
    
    function updateSelection(){
        if ($('#checkall')[0].checked) {
            $(".checker").each((key, value)=>{$(value).prop('checked', true);});
        }
        updateSelected();
    }
</script>
@endsection