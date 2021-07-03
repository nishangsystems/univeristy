@extends('admin.layout')
@section('title', 'Available incomes')
@section('section')
<div class="col-sm-12">
    <div class="col-sm-12">
        <div class="mb-3 d-flex justify-content-start">
            <h4 class="font-weight-bold">Income Details</h4>
        </div>
        <!-- <div class="text-muted mb-3 d-flex justify-content-end">
            <a href="{{route('admin.income.create')}}" class="btn btn-info btn-xs">Add income</a>
        </div> -->
    </div>
    <div class="content-panel">
        <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
            <div>
                <div>
                    <h5 class="font-weight-bold">Name : <span><label>{{$income->name}}</label></span></h5>
                </div>
                <div>
                    <h5 class="font-weight-bold">Amount : <span>
                            <label>{{number_format($income->amount)}} FCFA</label>
                        </span></h5>
                </div>
                <div>
                    @if($income->status == 1)
                    <h5 class="font-weight-bold">Status : <span><label>Active</label></span></h5>
                    @endif
                </div>
                <div>
                    @if($income->status != 1)
                    <h5 class="font-weight-bold">Status : <span><label>Inactive</label></span></h5>
                    @endif
                </div>
                <div class="d-inline-flex">
                </div>
            </div>
        </div>

    </div>
    @endsection