@extends('admin.layout')

@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.collect_boarding_fee.store',  [$class_id,$student_id])}}">

            <div class="form-group row">
                <label for="cname" class="control-label col-sm-2">Total Amount Payable: </label>
                <div class="col-sm-10">
                    <input for="cname" class="form-control" value="{{number_format($total_amount)}} CFA" disabled></input>
                </div>
            </div>
            {{-- <div class="form-group row">
                <label for="cname" class="control-label col-sm-2">Installment: <span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <select class="form-control section" name="installment_id">
                        <option value="">Select installment</option>
                        @foreach ($installments as $installment)
                        <option value="{{$installment->id}}">{{$installment->installment_name}},  {{number_format($installment->installment_amount)}} CFA</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2">Balance:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" id="balance" value="0 CFA" disabled/>
                </div>
            </div>
            @csrf

            <div class="form-group @error('amount_payable') has-error @enderror">
                <label for="cname" class="control-label col-sm-2">Deposit Payment(CFA): <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="amount_payable" value="{{old('amount_payable')}}" type="number" required />
                    @error('amount_payable')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('batch_id') has-error @enderror mt-4">
                <label class="control-label col-lg-2">Year <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="batch_id">
                        <option value="">Select year</option>
                        @foreach($years as $key => $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('batch_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>




            <div class="form-group">
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-sm btn-primary mx-3" type="submit">Save</button>
                    <a class="btn btn-sm btn-danger" type="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
    $('.section').on('change', function() {
        val = $(this).val();
        url = "{{route('admin.getTotalBoardingAmount', ':id')}}";
        search_url = url.replace(':id', val);
        $.ajax({
            type: 'GET',
            url: search_url,
            success: function(response) {
                let balance = response[0].amount_new_student - response[0].installment_amount;
                value = document.getElementById('balance').value = balance.toLocaleString() + ' CFA';
                //console.log(value);
            },
            error: function(e) {

            }
        })
    })
</script>
@endsection
