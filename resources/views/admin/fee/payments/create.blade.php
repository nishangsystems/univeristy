@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.fee.student.payments.store',$student->id)}}">
            <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.enter_fee_details')}}</h5>
            @csrf
            <div class="form-group row">
                <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.totoal_fee')}}: </label>
                <div class="col-sm-10">
                    <input for="cname" class="form-control" value="{{number_format($total_fee)}} CFA" disabled></input>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.scholarship_award')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" value="{{number_format($scholarship)}} CFA" disabled></input>
                </div>
            </div>
            <div class="form-group">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.balance_fee')}}:</label>
                <div class="col-lg-10">
                    <input for="cname" class="form-control" name="balance" value="{{number_format($balance)}} CFA" disabled></input>
                </div>
            </div>
            <div class="form-group @error('item') has-error @enderror">
                <label class="control-label col-lg-2 text-capitalize">{{__('text.word_item')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" name="item">
                        <option value="" disabled class="text-capitalize">{{__('text.select_item')}}</option>
                        @foreach($student->class(\App\Helpers\Helpers::instance()->getYear())->payment_items()->get() ?? [] as $item)
                        <option selected value="{{$item->id}}">{{$item->name." - ".$item->amount}} FCFA</option>
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
                <div class="d-flex justify-content-end col-lg-12">
                    <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger " href="{{route('admin.fee.student.payments.index', $student->id)}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>

        <div class="px-5 py-3">
            <div class="py-3 text-center fw-bold h4">
                Payment History
            </div>
            <div class="content-panel">
                @forelse($student->payments()->where(['batch_id'=>\App\Helpers\Helpers::instance()->getYear()])->get() as $item)
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <div>
                            <div>{{($item->item)?$item->item->name:$item->created_at->format('d/m/Y')}}</div>
                            <h4 class="font-weight-bold">{{$item->amount}} {{__('text.currency_cfa')}}</h4>
                        </div>
                        <div class="d-inline-flex">
                            <a href="{{route('admin.fee.student.payments.edit', [ $student->id, $item->id])}}" class="btn m-2 btn-sm btn-primary text-white text-capitalize">{{__('text.word_edit')}}</a>
    
                            <a onclick="event.preventDefault();
                                                document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-2 text-capitalize">{{__('text.word_delete')}}</a>
                            <form id="delete" action="{{route('admin.fee.student.payments.destroy',[$student->id,$item->id])}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <p>{{__('text.phrase_2', ['in_bold'=>__('text.collect_fee')])}}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
</div>
@endsection

