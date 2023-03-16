@extends('student.layout')
@section('section')
    @php
        $student = auth('student')->user();
        $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $header = \App\Helpers\Helpers::instance()->getHeader();
    @endphp

    <div class="col-sm-12">
        <div class="content-panel">
            @forelse($transactions as $item)
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <div>
                        <div>{{$item->payment_purpose.($item->semester_id > 0 ? ' - '.\App\Models\Semester::find($item->semester_id)->name : '').' : '.\App\Models\Batch::find($item->year_id)->name}}</div>
                        <h4 class="font-weight-bold">{{number_format($item->amount)}} FCFA</h4>
                        <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->created_at))}}</span>
                        <br>
                        <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.$item->financialTrancationId}}</span>
                    </div>
                    <!-- <btn class="btn btn-sm btn-primary" onclick="printDiv('printHERE{{$item->id}}')">{{__('text.word_print')}}</btn> -->
                    <!-- create a hidden div for printable markup and print with js on request -->
                    <div class="d-none">
                        
                    </div>
                    <!-- ------------------------------- -->


                </div>
            @empty
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <p>No Payments where found </p>
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