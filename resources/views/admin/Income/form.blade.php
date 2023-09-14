<div class="row">
    <div class="col-sm-3">
        <label for="cname" class="control-label text-capitalize">{{__('text.word_name')}} <span style="color:red">*</span></label>
        <div class="form-group text-uppercase @error('name') has-error @enderror">
            <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
            @error('name')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-3">
        <label for="cname" class="control-label text-capitalize">{{__('text.word_amount')}} <span style="color:red">*</span></label>
        <div class="form-group text-uppercase @error('amount') has-error @enderror">
            <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required />
            @error('amount')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-3">
        <label for="cname" class="control-label text-capitalize">{{__('text.pay_online')}} <span style="color:red">*</span></label>
        <div class="form-group text-uppercase @error('pay_online') has-error @enderror">
            <select class=" form-control text-uppercase" name="pay_online" required >
                <option value=""></option>
                <option value="1" {{old('pay_online') == 1 ? 'selected' : ''}}>{{__('text.word_yes')}}</option>
                <option value="0" {{old('pay_online') == 0 ? 'selected' : ''}}>{{__('text.word_no')}}</option>
            </select>
            @error('amount')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group col-sm-2">
        <div class="d-flex justify-content-end  ">
            <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>

        </div>
    </div>
</div>