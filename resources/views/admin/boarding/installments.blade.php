@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="form-panel mb-5 ml-2">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.boarding_fee.installments.store', $id)}}">
            <div class="form-group ">
                <label for="cname" class="control-label col-sm-2 text-capitalize">{{__('text.installment_name')}}<span style="color:red">*</span></label>
                <div class="col-sm-3">
                    <div class="form-group @error('installment_name') has-error @enderror">
                        <input class=" form-control" name="installment_name" value="{{old('installment_name')}}" type="text" required />
                        @error('installment_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <label for="cname" class="control-label col-sm-2 text-capitalize ">{{__('text.word_amount')}} <span style="color:red">*</span></label>
                <div class="col-md-3">
                    <div class="form-group @error('installment_amount') has-error @enderror">
                        <input class=" form-control" name="installment_amount" value="{{old('installment_amount')}}" type="number" required />
                        @error('installment_amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="d-flex justify-content-end ">
                        <button id="save" class="btn btn-sm btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>

                    </div>
                </div>
            </div>
            @csrf
        </form>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_installment')}}</th>
                        <th>{{__('text.word_amount')}} (CFA)</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($boarding_fee_installments as $k=>$installment)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$installment->installment_name}}</td>
                        <td>{{number_format($installment->installment_amount)}}</td>
                        <td class="d-flex justify-content-end  align-items-center">
                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.boarding_fee.installments.edit',[$installment->boarding_fee_id, $installment->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash"> {{__('text.word_delete')}}</i></a>
                            <form id="delete" action="{{route('admin.boarding_fee.installments.destroy',[$installment->boarding_fee_id, $installment->id])}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection

