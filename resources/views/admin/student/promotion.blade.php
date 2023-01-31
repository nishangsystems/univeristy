@extends('admin.layout')
@section('section')
<div class="container-fluid h-screen d-flex flex-column justify-content-center">
    <div class="w-100 d-block py-3  rounded-lg bg-light">
            <h2 class="my-3 text-dark fw-bolder text-center w-100">{{$title}}</h2>
            <div class="w-100 py-2 d-md-flex text-capitalize">
                <div class="w-50 px-4">
                    <h3 class="py-1 fw-bold text-dark">{{__('text.academic_year')}}</h3>
                    <div class="form-group w-100 py-1">
                        <label for="year_from" class="text-secondary">{{__('text.word_from')}}:</label>
                        <span id="" class="form-control text-dark rounded">{{\App\Models\Batch::find($current_year)->name ?? '----' }}</span>
                    </div>
                    <div class="form-group w-100 py-1">
                        <label for="year_to" class="text-secondary">{{__('text.word_to')}}:</label>
                        <span id="" class="form-control text-dark rounded">{{\App\Models\Batch::find($current_year+1)->name  ?? '----'}}</span>
                    </div>
                </div>
                <div class="w-50 px-4">
                    <h3 class="py-1 fw-bold text-dark">{{__('text.word_class')}}</h3>
                    <div class="form-group w-100 py-1">
                        <label for="class_from" class="text-secondary">{{__('text.word_from')}}:</label>
                        <span id="" class="form-control text-dark rounded">{{$classes['cf']['name']}}</span>
                    </div>
                    <div class="form-group w-100 py-1">
                        <label for="class_to" class="text-secondary">{{__('text.word_to')}}:</label>
                        <span id="" class="form-control text-dark rounded">{{$classes['ct']['name']}}</span>
                    </div>
                </div>
            </div>
    </div>
    <form action="{{route('admin.students.promote')}}" method="post" class="w-100" id="promote_form">
        @csrf
        <input type="hidden" name="class_from" value="{{request('class_from')}}">
        <input type="hidden" name="class_to" value="{{request('class_to')}}">
        <input type="hidden" name="year_from" value="{{$current_year}}">
        <input type="hidden" name="year_to" value="{{$current_year+1}}">
        @if(request('promotion_batch') != '') <input type="hidden" name="type" value="demotion" id="">
        @else <input type="hidden" name="type" value="promotion" id="">
        @endif
        <div class="w-100 py-2">
            <div class="d-flex justify-content-center align-moddle">
                <div class="mx-4">
                    <label for="total" class="text-secondary text-capitalize">{{__('text.word_total')}}:</label>
                    <span class="text-dark fw-bold fs-3" id="student-total">{{count($students)}}</span>
                </div>
                <div class="mx-4">
                    <label for="total" class="text-secondary text-capitalize">{{__('text.word_selected')}}:</label>
                    <span class="text-dark fw-bold fs-3" id="selected">-</span>
                </div>
            </div>
            <div class="w-100">
                
                <table class="w-100 table-striped">
                    <thead class="w-100 bg-secondary text-light text-capitalize">
                        <th class="border-left border-right"><input type="checkbox" value="1" id="checkall" onchange="updateSelection(event)"><span>{{__('text.word_all')}}</span></th>
                        <th class="border-left border-right">{{__('text.sn')}}</th>
                        <!-- <th class="border-left border-right">Matric</th> -->
                        <th class="border-left border-right">{{__('text.word_name')}}</th>
                        <th class="border-left border-right">{{__('text.word_matricule')}}</th>
                        <th class="border-left border-right">{{__('text.word_average')}}</th>
                        @php $sn = 0; @endphp
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="fw-bolder fs-3 text-dark border-bottom">
                            <td class="border-left border-right"><input type="checkbox" class="checker" name="students[]" value="{{$student->id}}" id="" onchange="updateSelected()"></td>
                            <td class="border-left border-right">{{++$sn}}</td>
                            <!-- <td class="border-left border-right"><span>{{$student->matric}}</span></td> -->
                            <td class="border-left border-right"><span>{{$student->name}}</span></td>
                            <td class="border-left border-right"><span>{{$student->matric}}</span></td>
                            <td class="border-left border-right"><span onload="getAverage(this, '{{$student->id}}')">--</span></td>
                        </tr>
                        @empty
                        <p class="text-center h5">No students available for promotion in this class.</p>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button onclick="confirm(`You are about to promote ${$('.checker:checked').length} students from {{$classes['cf']['name']}} : {{\App\Models\Batch::find($current_year)->name ?? '----' }} to {{$classes['ct']['name']}} : {{\App\Models\Batch::find($current_year+1)->name  ?? '----'}}`) ? $('#promote_form').submit() : ''" class="btn btn-primary mx-4 my-2">{{__('text.word_promote')}}</button>
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
        }else{
            $(".checker").each((key, value)=>{$(value).prop('checked', false);});
        }
        updateSelected();
    }
</script>
@endsection