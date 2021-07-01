@extends('student.layout')
@section('section')
@php
$student = Auth('student')->user();
@endphp
<div class="col-sm-12">
    <div class="form-panel mb-5 ml-2">
        <form class="form-horizontal" role="form" method="POST" action="{{route('student.boarding_fee.per_year')}}">
            <div class="form-group @error('batch_id') has-error @enderror mt-3">
                <div class="col-lg-12">

                    <select class="form-control col-md-8" name="batch_id">
                        <option value="">Select year</option>
                        @foreach($years as $key => $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('batch_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <label class="control-label "> <button class="btn btn-md btn-primary" type="submit">Get Boarding Fees</button></label>
                    </div>
                </div>

            </div>
            @csrf
        </form>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" style="padding: 20px; background: #ffffff; " id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Matricule</th>
                        <th>Class</th>
                        <th>Amount Payable(CFA)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boarding_fees as $k=>$boarding_fee)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$boarding_fee->name}}</td>
                        <td>{{$boarding_fee->matric}}</td>
                        <td>{{$boarding_fee->class_name}}</td>
                        <td>{{number_format($boarding_fee->amount_payable)}}</td>
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
                {{$boarding_fees->links()}}
            </div>
        </div>
    </div>
</div>
@stop