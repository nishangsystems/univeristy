@extends('api.layout')
@section('section')
    @php
        $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $header = \App\Helpers\Helpers::instance()->getHeader();
    @endphp

    <div class="col-sm-12">
        <div class="d-flex flex-wrap justify-content-between alert alert-info text-center text-uppercase my-3"><span>{{__('text.total_paid').' : '.number_format($student->total_paid( $year)).' '.__('text.currency_cfa')}}</span><span>{{__('text.total_debts').' : '.number_format($student->bal($student->id, $year)).' '.__('text.currency_cfa')}}</span></div>

        <div class="content-panel">
            @forelse($student->payments()->where(['batch_id'=>(request('year') ?? \App\Helpers\Helpers::instance()->getYear())])->get() as $item)
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <div>
                        <div>{{($item->item) ? $item->item->name : $item->created_at}}</div>
                        <h4 class="font-weight-bold">{{number_format($item->amount)}} FCFA</h4>
                        <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->created_at))}}</span>
                        <br>
                        <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.$item->reference_number}}</span>
                    </div>
                </div>
                @if($item->debt != 0 && $item->debt != null)
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <div>
                            <div>{{__('text.word_debt')}}</div>
                            <h4 class="font-weight-bold">{{number_format($item->debt)}} FCFA</h4>
                            <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->created_at))}}</span>
                            <br>
                            <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.$item->reference_number}}</span>
                        </div>
                    </div>
                @endif
            @empty
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <p>No Fee Collection where found, for <b>{{\App\Models\Batch::find($year)->name}}</b> </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
@section('script')
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection