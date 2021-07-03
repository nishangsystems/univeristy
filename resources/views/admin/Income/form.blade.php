<div class="row">
    <div class="col-sm-2 d-flex justify-content-md-center">
        <label for="cname" class="control-label">Name <span style="color:red">*</span></label>
    </div>
    <div class="form-group @error('name') has-error @enderror col-sm-3">
        <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
        @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-sm-2 d-flex justify-content-md-center">
        <label for="cname" class="control-label">Amount <span style="color:red">*</span></label>
    </div>
    <div class="form-group @error('amount') has-error @enderror col-sm-3">
        <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required />
        @error('amount')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
    <div class="form-group col-sm-2">
        <div class="d-flex justify-content-end  ">
            <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">Save</button>

        </div>
    </div>
</div>