@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.fee.student.payments.store',$student->id)}}">
                <h5 class="mt-5 font-weight-bold">Enter Fee Details</h5>
                @csrf

                <div class="form-group @error('item') has-error @enderror">
                    <label  class="control-label col-lg-2">Item</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="item">
                            <option value="" disabled>Select Item</option>
                            <option value="0">Other Items</option>
                            @foreach($student->class(\App\Helpers\Helpers::instance()->getYear())->items as $item)
                                <option {{old('item') == $item->id?'selected':''}} value="{{$item->id}}">{{$item->name." - ".$item->amount}} FCFA</option>
                            @endforeach
                        </select>
                        @error('item')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group @error('amount') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">Amount (required)</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required/>
                        @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">Save</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.fee.student.payments.index', $student->id)}}" type="button">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
