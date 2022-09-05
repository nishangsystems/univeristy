@extends('admin.layout')

@section('section')
<div class="col-sm-12">
    <div class="col-sm-12">
        <!-- <div class="mb-3 d-flex justify-content-start">
            <h4 class="font-weight-bold">expense Details</h4>
        </div> -->
        <!-- <div class="text-muted mb-3 d-flex justify-content-end">
            <a href="{{route('admin.expense.create')}}" class="btn btn-info btn-xs">Add expense</a>
        </div> -->
    </div>
    <div class="content-panel">
        <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
            <div>
                <div>
                    <h5 class="font-weight-bold text-capitalize">{{__('text.word_name')}} : <span><label>{{$expense->name}}</label></span></h5>
                </div>
                <div>
                    <h5 class="font-weight-bold text-capitalize">{{__('text.amount_spent')}}: <span>
                            <label>{{number_format($expense->amount_spend)}} {{__('text.currency_cfa')}}</label>
                        </span></h5>
                </div>
                <div>
                    <h5 class="font-weight-bold text-capitalize">{{__('text.expense_date')}}: <span><label>{{date('jS F Y', strtotime($expense->date))}}</label></span></h5>
                </div>


                <div class="d-inline-flex">
                </div>
            </div>
        </div>

    </div>
    @endsection
