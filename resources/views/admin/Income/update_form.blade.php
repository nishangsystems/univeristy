<div class="form-group @error('name') has-error @enderror ">
    <label for="cname" class="control-label col-lg-2">{{__('text.word_name')}} <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="name" value="{{old('name') ?? $income->name}}" type="text" required />
        @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group @error('amount') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">{{__('text.word_amount')}} <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="amount" value="{{old('amount') ?? $income->amount}}" type="number" required />
        @error('amount')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group @error('pay_online') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">{{__('text.pay_online')}} <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <select class=" form-control" name="pay_online" required >
            <option></option>
            <option value="1" {{$income->pay_online == 1 ? 'selected' : ''}}>{{__('text.word_yes')}}</option>
            <option value="0" {{$income->pay_online == 0 ? 'selected' : ''}}>{{__('text.word_no')}}</option>
        </select>
        @error('pay_online')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group">
    <div class="d-flex justify-content-end col-lg-12">
        <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
        <a class="btn btn-xs btn-danger" href="#" type="button">{{__('text.word_cancel')}}</a>
    </div>
</div>