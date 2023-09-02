@extends('parents.layout')
@section('section')
    @php
        $student = auth('student')->user()
    @endphp

    <div class="col-sm-12">
        <div class="content-panel">
            <form method="get">
                <div class="input-group input-group-merge border">
                    <select name="year" id="" class="form-control border-0" required>
                        <option value="">{{__('text.academic_year')}}</option>
                        @foreach(\App\Models\Batch::all() as $batch)
                            <option value="{{$batch->id}}">{{$batch->name}}</option>
                        @endforeach
                    </select>
                    <input type="submit" class="btn btn-sm btn-light border-0 rounded-0" value="{{__('text.word_get')}}">
                </div>
            </form>
        </div>
        <div class="content-panel">
            @forelse($student->payIncomes((request('year') ?? \App\Helpers\Helpers::instance()->getYear()))->get() as $item)
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <div>
                        <div>{{$item->income->name}}</div>
                        <h4 class="font-weight-bold">{{number_format($item->income->amount)}} FCFA</h4>
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
