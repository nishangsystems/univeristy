@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.collect_boarding_fee.update',[$boarding_fee->id, $student_id])}}">
            @csrf
            @method('PUT')
            <div class="form-group row">
                <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.amount_paid')}} :</label>
                <div class="col-sm-10">
                    <input for="cname" class="form-control" value="{{number_format($boarding_fee->total_amount)}} CFA" disabled/>
                </div>
            </div>
            <div class="form-group row">
                <label for="cname" class="control-label col-lg-2">{{__('text.balance_payment')}} :</label>
                <div class="col-sm-10">
                    <input for="cname" class="form-control"  value="{{number_format($balance)}} CFA" disabled/>
                </div>
            </div>
            <div class="form-group @error('amount_payable') has-error @enderror">
                <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.deposit_payament')}} {{__('text.currency_cfa')}} <span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input class=" form-control" name="amount_payable" type="number" required />
                    @error('amount_payable')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-sm btn-primary mx-3" type="submit">Save</button>
                    <a class="btn btn-sm btn-danger" href="#" type="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

