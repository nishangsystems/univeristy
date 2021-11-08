@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.boarding_fee.installments.update', [$installment->boarding_fee_id,$installment->id])}}">
            @csrf
            @method('PUT')
            <div class="form-group @error('installment_name') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Installlment <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="installment_name" value="{{old('installment_name') ?? $installment->installment_name}}" type="text" required />
                    @error('installment_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('installment_amount') has-error @enderror">
                <label for="cname" class="control-label col-lg-2">Amount<span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="installment_amount" value="{{old('installment_amount') ?? $installment->installment_amount}}" type="number" required />
                    @error('installment_amount')
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
