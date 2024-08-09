@extends('admin.layout')
@php
    $c_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $header = \App\Helpers\Helpers::instance()->getHeader();
@endphp
@section('section')
<div class="mx-3">
    <div class="form-panel row">
        <div class="col-lg-6 my-3">
            @if($student->total_balance() > 0)
                <form class="form-horizontal" role="form" method="POST" action="{{route('admin.fee.student.payments.store',$student->id)}}">
                    <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.enter_fee_details')}}</h5>
                    @csrf
                    <div class="form-group row">
                        <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.total_fee')}}: </label>
                        <div class="col-sm-10">
                            <input for="cname" class="form-control" value="{{number_format($total_fee)}} CFA" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.scholarship_award')}}:</label>
                        <div class="col-lg-10">
                            <input for="cname" class="form-control" value="{{number_format($scholarship)}} CFA" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.balance_fee')}}:</label>
                        <div class="col-lg-10">
                            <input for="cname" class="form-control" name="balance" value="{{number_format($student->bal(null, $c_year))}} CFA" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.extra_fee')}}:</label>
                        <div class="col-lg-10">
                            <input for="cname" class="form-control" name="xtra-fee" value="{{$student->extraFee($c_year) == null ? 0 : $student->extraFee($c_year)->amount}} CFA" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_debt')}}:</label>
                        <div class="col-lg-10">
                            <input for="cname" class="form-control" name="xtra-fee" value="{{$student->total_debts($c_year)}} CFA" disabled></input>
                        </div>
                    </div>
                    <div class="form-group @error('year') has-error @enderror">
                        <label class="control-label col-lg-2 text-capitalize">{{__('text.academic_year')}} <span style="color:red">*</span></label>
                        <div class="col-lg-10">
                            <select class="form-control" name="year" aria-placeholder="default">
                                <option class="text-capitalize"></option>
                                @foreach($years as $year)
                                    <option value="{{$year->id}}" {{ $year->id == $c_year ? 'selected' : '' }}>{{$year->name}}</option>
                                @endforeach
                            </select>
                            @error('year')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group @error('year') has-error @enderror">
                        <label class="control-label col-lg-2 text-capitalize">{{__('text.word_bank')}}</label>
                        <div class="col-lg-10">
                            <select class="form-control" name="bank_id" aria-placeholder="default">
                                <option class="text-capitalize"></option>
                                @foreach($banks as $bank)
                                    <option value="{{$bank->id}}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>{{$bank->name}}</option>
                                @endforeach
                            </select>
                            @error('year')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group @error('item') has-error @enderror">
                        <label class="control-label col-lg-2 text-capitalize">{{__('text.word_item')}} <span style="color:red">*</span></label>
                        <div class="col-lg-10">
                            <select class="form-control" name="item">
                                <option value="" disabled class="text-capitalize">{{__('text.select_item')}}</option>
                                @foreach($student->class(\App\Helpers\Helpers::instance()->getYear())->payment_items()->where(['year_id'=>$c_year])->get() ?? [] as $item)
                                    <option value="{{$item->id}}">{{$item->name." - ".$item->amount}} FCFA</option>
                                @endforeach
                            </select>
                            @error('item')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group @error('amount') has-error @enderror">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_amount')}} <span style="color:red">*</span></label>
                        <div class="col-lg-10">
                            <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required />
                            @error('amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_date')}}<span style="color:red">*</span></label>
                        <div class="col-lg-10">
                            <input class=" form-control" name="date" value="{{old('amount')}}" type="date" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.reference_number')}}</label>
                        <div class="col-lg-10">
                            <input class=" form-control" name="reference_number" value="{{old('reference_number')}}" type="text" placeholder="{{__('text.word_optional')}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-end col-lg-12">
                            <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>
                            <a class="btn btn-xs btn-danger " href="{{route('admin.fee.student.payments.index', $student->id)}}" type="button">{{__('text.word_cancel')}}</a>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-success text-center text-capitalize">{{__('text.phrase_fee_complete')}} <span class="mx-3 fw-bolder">Total Debt: {{$student->total_debts($c_year)}} | Current Fee : {{number_format($total_fee)}}</span></div>
            @endif
        </div>
        <div class="col-lg-6 my-3">
            <div class="px-5 py-3 shadow">
                <div class="py-3 text-center fw-bold h4">
                    Payment History
                </div>
                <div class="content-panel">
                    <table class="table">
                        <thead class="text-capitalize">
                            <th>###</th>
                            <th>{{__('text.word_item')}}</th>
                            <th>{{__('text.word_amount')}}</th>
                            <th>{{__('text.word_bank')}}</th>
                            <th>{{__('text.word_debt')}}</th>
                            <th>{{__('text.word_date')}}</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @php($k=1)
                            @forelse($student->payments()->where(['payment_year_id'=>$c_year])->orderBy('id', 'DESC')->get() as $item)

                            <!-- <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end"> -->
                                <tr>
                                    <td>{{$k++}}</td>
                                    <td>{{($item->item)?$item->item->name:$item->created_at->format('d/m/Y')}}</td>
                                    <td class="font-weight-bold">{{$item->amount}} {{__('text.currency_cfa')}}</td>
                                    <td class="font-weight-bold">{{ optional($item->bank)->name??'---' }}</td>
                                    <td class="font-weight-bold">{{$item->debt}} {{__('text.currency_cfa')}}</td>
                                    <td>{{$item->created_at->format('l d/m/Y')}}</td>
                                    <td class="d-inline-flex">
                                        <!-- <a href="{{route('admin.fee.student.payments.edit', [ $student->id, $item->id])}}" class="btn m-2 btn-sm btn-primary text-white text-capitalize">{{__('text.word_edit')}}</a> -->
                
                                        @if($item->user_id == auth()->id())
                                            <a onclick="event.preventDefault(); delete_fee_item('delete-{{$item->id}}-form', 'delete-{{$item->id}}-reason')" class=" btn btn-danger btn-sm m-2 text-capitalize">{{__('text.word_delete')}}</a>
                                            <form id="delete-{{$item->id}}-form" action="{{route('admin.fee.student.payments.destroy',[$student->id,$item->id])}}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                {{ csrf_field() }}
                                                <input type="hidden" name="reason" id="delete-{{$item->id}}-reason">
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                                    <p>{{__('text.phrase_2', ['in_bold'=>__('text.collect_fee')])}}</p>
                                </div>
                            @endforelse
                            
                        </tbody>
                    </table>
                    @if($k > 1)
                        <div class="d-flex justify-content-end my-2">
                            <!-- <a class="btn btn-sm btn-primary text-capitalize" href="{{route('admin.fee.student.payments.print', [ $student->id, $item->id])}}">{{__('text.word_print')}}</a> -->
                            <button class="btn btn-sm btn-primary text-capitalize" onclick="_print()">{{__('text.word_print')}}</button>
                            <div class="hidden" id="payment_history_printable">
                                <div class="text-center">
                                    <img src="{{$header}}" alt="" class="w-100">
                                    <div class="py-2 h4 text-decoration text-decoration-italic  text-decoration-underline text-uppercase">FEE payment history for {{$student->name}}</div>
                                </div>
                                <table>
                                    <thead class="text-capitalize">
                                        <th>###</th>
                                        <th>{{__('text.word_item')}}</th>
                                        <th>{{__('text.word_amount')}}</th>
                                        <th>{{__('text.word_date')}}</th>
                                        <!-- <th></th> -->
                                    </thead>
                                    <tbody>
                                        @php($k=1)
                                        @foreach($student->payments()->where(['payment_year_id'=>$c_year])->get() as $item)
                                        <!-- <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end"> -->
                                        <tr>
                                            <td>{{$k++}}</td>
                                            <td>{{($item->item)?$item->item->name:$item->created_at->format('d/m/Y')}}</td>
                                            <td class="font-weight-bold">{{$item->amount}} {{__('text.currency_cfa')}}</td>
                                            <td>{{$item->created_at->format('l d/m/Y')}}</td>
                                        </tr>
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="py-2 mb-5 px-3"><a class="btn btn-primary rounded" href="{{ route('admin.fee.history', $student->id) }}">Detail payment history</a></div>
            </div>
        </div>

    </div>
    
</div>
@endsection

@section('script')
<script>
    function _print() {
        var _this_doc, _printable_doc;
        _this_doc = document.body.innerHTML;
        _printable_doc = document.querySelector('#payment_history_printable').innerHTML;
        document.body.innerHTML = _printable_doc;
        window.print();
        document.body.innerHTML = _this_doc;
    }

    let delete_fee_item = function(form_id, field_id){
        let reason = prompt("SPECIFY THE REASON FOR DELETING THIS FEE RECORD:");
        if(reason == "" || reason == null){alert("YOU CAN'T DELETE A FEE RECORD WITHOUT SPECIFYING THE REASON"); return;}
        $('#'+field_id).val(reason);
        $('#'+form_id).submit();

    }
</script>
@endsection