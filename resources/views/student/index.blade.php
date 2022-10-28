@extends('student.layout')

@section('section')
<div class="col-sm-12">
    <div class="col-lg-12">
        <div class="form-panel mb-3 ml-3 mt-5 mb-5">
            <form class="form-horizontal" role="form" method="POST" action="{{route('student.boarding_fees_details')}}">
                <div class="form-group @error('class_id') has-error @enderror ">
                    <div class="col-lg-2">

                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-lg-2">

                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-lg-2">

                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-lg-2">
                        <select class="form-control" name="batch_id">
                            <option value="">Select Year</option>
                            @foreach($years as $key => $year)
                            <option value="{{$year->id}}">{{$year->name}}</option>
                            @endforeach
                        </select>
                        @error('batch_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class=" col-lg-1 mb-1">
                        <button class="btn btn-xs btn-primary" id="submit" type="submit">Get Details</button>
                    </div>
                </div>
                @csrf
            </form>
        </div>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Matricule</th>
                        <th>Amount Payable(CFA)</th>
                        <th>Total Amount Paid(CFA)</th>
                        <th>Balance(CFA)</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paid_boarding_fee_details as $k=>$boarding_fee)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$boarding_fee->name}}</td>
                        <td>{{$boarding_fee->matric}}</td>
                        <td>{{number_format($boarding_fee->amount_payable)}}</td>
                        <td>{{number_format($boarding_fee->total_amount)}}</td>
                        <td>{{number_format($boarding_fee->balance)}}</td>
                        <td>{{date('jS F Y', strtotime($boarding_fee->created_at))}}</td>
                        @if($boarding_fee->status == 0)
                        <td>Incomplete</td>
                        @endif
                        @if($boarding_fee->status == 1)
                        <td>Completed</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{$paid_boarding_fee_details->links()}}
            </div>
        </div>
    </div>
</div>
@endsection