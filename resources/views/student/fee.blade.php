@extends('student.layout')
@section('section')
    @php
        $student = \Auth::user()
    @endphp

    <div class="col-sm-12">
        <div class="content-panel">
            @forelse($student->payments()->where(['batch_id'=>\App\Helpers\Helpers::instance()->getYear()])->get() as $item)
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <div>
                        <div>{{($item->item) ? $item->item->name : $item->created_at}}</div>
                        <h4 class="font-weight-bold">{{number_format($item->amount)}} FCFA</h4>
                        <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->created_at))}}</span>
                        <br>
                        <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.$item->reference_number}}</span>
                    </div> 
                </div>
            @empty
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <p>No Fee Collection where found, for <b>{{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getYear())->name}}</b> </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
