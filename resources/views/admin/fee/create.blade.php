@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $feebuilder = \App\Models\CampusProgram::where('campus_id', request('id'))->where('program_level_id', request('program_id'))->first()->payment_items()->where('year_id', $year)->get();
@endphp
<div class="py-4">
    <div class="py-3 border-top border-bottom text-center h4 fw-bold text-primary" style="background-color: #fefefe;">
        @if($feebuilder->where('name', 'TUTION')->count()==0)
            Fees not set for this program
        @else
            <span class="text-capitalize">{{__('text.word_tution')}}</span> : {{$feebuilder->where('name', 'TUTION')->first()->amount}}
        @endif
    </div>
    <div class="py-3 border-top border-bottom text-center h4 fw-bold text-primary" style="background-color: #fefefe;">
        @if($feebuilder->where('name', 'REGISTRATION')->count()==0)
            Registration Fees not set for this program
        @else
            <span class="text-capitalize">{{__('text.word_registration')}}</span> : {{$feebuilder->where('name', 'REGISTRATION')->first()->amount}}
        @endif
    </div>
    <div class="py-3">
        <form action="{{route('admin.campuses.set_fee', [request('id'), request('program_id')])}}" method="post">
            @csrf
            <div class="row my-2 text-capitalize">
                <label for="" class="col-md-3 form-group-text">{{__('text.word_tution')}}</label>
                <div class="col-md-9 col-lg-9">
                    <input type="number" name="fees" id="" class="form-control" id="field" required
                    value="{{
                        $feebuilder->where('name', 'TUTION')->count()==0 ?
                        '----' :
                        $feebuilder->where('name', 'TUTION')->first()->amount
                    }}">
                </div>
            </div>
            <div class="row my-2 text-capitalize">
                <label for="" class="col-md-3 form-group-text">{{__('text.word_registration')}}</label>
                <div class="col-md-9 col-lg-9">
                    <input type="number" name="r_fees" id="" class="form-control" id="field"
                    value="{{
                        $feebuilder->where('name', 'REGISTRATION')->count()==0 ?
                        '----' :
                        $feebuilder->where('name', 'REGISTRATION')->first()->amount
                    }}">
                </div>
            </div>
            <div class="d-flex justify-content-end py-2">
                <button class="btn btn-sm btn-primary" type="submit">{{__('text.word_update')}}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    function verifyUpdate(form) {
        event.preventDefault();
        let flag = prompt("You are about to change the fee setting. Enter 'CONFIRM' to confirm change");
        if(flag=="CONFIRM"){
            form.submit();
        }
        else{
            form.reset();
        }
    }
</script>
@endsection