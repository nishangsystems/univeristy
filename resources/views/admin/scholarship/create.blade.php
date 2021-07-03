<div class="form-panel">
    <form class="form-horizontal" role="form" method="POST" action="{{route('admin.scholarship.store')}}">
        @csrf
        <div class="row">
            <div class="col-sm-1">
                <label for="cname" class="control-label">Name <span style="color: red;">*</span></label>
            </div>
            <div class="form-group @error('name') has-error @enderror col-sm-2">
                <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
                @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-1">
                <label for="cname" class="control-label">Amount <span style="color: red;">*</span></label>
            </div>
            <div class="form-group @error('amount') has-error @enderror col-sm-2">
                <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required />
                @error('amount')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-2">
                <label class="control-label ">Scholarship Type <span style="color: red;">*</span></label>
            </div>
            <div class="form-group @error('type') has-error @enderror col-sm-3">
                <select class="form-control" name="type">
                    <option value="">Select Type</option>
                    <option value="1">Tuition Fee Only</option>
                    <option value="2">Partial Tuition Fee</option>
                    <option value="3">Partial Boarding Fee</option>
                    <option value="4">Boarding Fee Only</option>
                    <option value="5">Student Expenses(PTA, T-shirts, Sporting Materials)</option>
                    <option value="6">Full-time</option>
                </select>
                @error('type')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-sm-1">
                <div class="d-flex justify-content-end">
                    <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">Save</button>

                </div>
            </div>
        </div>
    </form>
</div>