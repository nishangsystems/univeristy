<div class="form-group @error('name') has-error @enderror ">
    <label for="cname" class="control-label col-lg-2">Name <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
        @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group @error('amount_spend') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">Amount Spend <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="amount_spend" value="{{old('amount_spend')}}" type="number" required />
        @error('amount_spend')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group @error('balance') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">Balance <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="balance" value="{{old('balance')}}" type="number" required />
        @error('balance')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group @error('date') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">Expense date <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="date" value="{{old('date')}}" type="date" required />
        @error('date')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group @error('description') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">Description <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <textarea class=" form-control" name="description" value="{{old('description')}}" type="textarea" placeholder="Please give a description about the expense" rows="5"></textarea>
        @error('description')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group">
    <div class="d-flex justify-content-end col-lg-12">
        <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">Save</button>
        <a class="btn btn-xs btn-danger" href="#" type="button">Cancel</a>
    </div>
</div>