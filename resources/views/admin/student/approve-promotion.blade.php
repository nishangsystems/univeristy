@extends('admin.layout')
@section('section')
<div class="container-fluid">
    <div class="my-4 py-4">
        <h2 class="text-dark fw-bolder text-center py-2 text-capitalize">{{__('text.promotion_approval')}}</h2>
        <div class="py-2">
            <table class="table table-stripped">
                <thead class="text-capitalize">
                    <th class="border-left border-right">###</th>
                    <th class="border-left border-right">{{__('text.word_from')}}</th>
                    <th class="border-left border-right">{{__('text.word_to')}}</th>
                    <th class="border-left border-right">{{__('text.word_date')}}</th>
                    <th class="border-left border-right">...</th>
                </thead>
                @php($count = 1)
                @foreach(\App\Models\PendingPromotion::all() as $pp)
                    <tr class="border-bottom">
                        <td class="border-eft border-right">{{$count++}}</td>
                        <td class="border-eft border-right">{{$classes[$pp->from_class]}} <span class="px-3 mx-3 text-info">{{\App\Models\Batch::find($pp->from_year)->name}}</span></td>
                        <!-- <td class="border-eft border-right">{{$classes[$pp->from_class]}}</td> -->
                        <td class="border-eft border-right">{{$classes[$pp->to_class]}} <span class="px-3 mx-3 text-info">{{\App\Models\Batch::find($pp->to_year)->name}}</span></td>
                        <td class="border-eft border-right">{{$pp->created_at}}</td>
                        <td class="border-eft border-right"><a href="{{route('admin.students.trigger_approval', [$pp->id])}}" class="btn btn-primary">&hookrightarrow;</a></td>
                    </tr>
                @endforeach
                @if(count(\App\Models\PendingPromotion::all()) == 0)
                    <div class="py-2 text-center bg-light text-dark fs-1 fw-bold">{{__('text.phrase_7')}}</div>
                @endif
            </table>
        </div>
        <div class="w-100 py-4">
            @if(isset($students))
                <form action="{{route('admin.students.approve_promotion')}}" method="post" class="w-100">
                    @csrf
                    <input type="hidden" name="pending_promotion" value="{{request('promotion_id')}}">
                    <table class="w-100 table-striped">
                        <thead class="w-100 bg-secondary text-light text-capitalize">
                            <th class="border-left border-right">{{__('text.sn')}}</th>
                            <th class="border-left border-right"><input type="checkbox" value="1" id="checkall" onchange="updateSelection(event)"><span>{{__('text.word_all')}}</span></th>
                            <!-- <th class="border-left border-right">Matric</th> -->
                            <th class="border-left border-right">{{__('text.word_matricule')}}</th>
                            <th class="border-left border-right">{{__('text.word_name')}}</th>
                            <th class="border-left border-right">{{__('text.word_average')}}</th>
                            @php($sn = 0)
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr class="fw-bolder fs-3 text-dark border-bottom">
                                <td class="border-left border-right">{{++$sn}}</td>
                                <td class="border-left border-right"><input type="checkbox" class="checker" name="students[]" value="{{$student->id}}" id="" onchange="updateSelected()"></td>
                                <!-- <td class="border-left border-right"><span>{{$student->matric}}</span></td> -->
                                <td class="border-left border-right"><span>{{$student->matric}}</span></td>
                                <td class="border-left border-right"><span>{{$student->name}}</span></td>
                                <td class="border-left border-right"><span>----</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
                        <a href="{{route('admin.students.cancel_promotion', request('promotion_id'))}}" class="btn btn-danger mx-4 my-2">{{__('text.word_cancel')}}</a>
                        <button type="submit" class="btn btn-success mx-4 my-2">{{__('text.word_promote')}}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
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