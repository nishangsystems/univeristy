<div class="form-group @error('name') has-error @enderror ">
    <label for="cname" class="control-label col-lg-2">Name <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="name" value="{{old('name') ?? $expense->name}}" type="text" required />
        @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="form-group @error('amount_spend') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">Amount <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <input class=" form-control" name="amount_spend" value="{{old('amount_spend') ?? $expense->amount_spend}}" type="number" required />
        @error('amount_spend')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-group @error('description') has-error @enderror">
    <label for="cname" class="control-label col-lg-2">Description <span style="color:red">*</span></label>
    <div class="col-lg-10">
        <textarea class=" form-control" name="description" type="textarea" rows="5">{{old('description') ?? $expense->description }}</textarea>
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