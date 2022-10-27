<div class="form-panel">
    <form class="form-horizontal" role="form" method="POST" action="{{route('admin.scholarship.store')}}">
        @csrf
        <div class="row">
            <div class="col-sm-1">
                <label for="cname" class="control-label text-capitalize">{{__('text.word_name')}} <span style="color: red;">*</span></label>
            </div>
            <div class="form-group @error('name') has-error @enderror col-sm-2">
                <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
                @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-1">
                <label for="cname" class="control-label text-capitalize">{{__('text.word_amount')}} <span style="color: red;">*</span></label>
            </div>
            <div class="form-group @error('amount') has-error @enderror col-sm-2">
                <input class=" form-control" name="amount" value="{{old('amount')}}" type="number" required />
                @error('amount')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-2">
                <label class="control-label text-capitalize">{{__('text.scholarship_type')}} <span style="color: red;">*</span></label>
            </div>
            <div class="form-group @error('type') has-error @enderror col-sm-3">
                <select class="form-control text-capitalize" name="type">
                    <option value="">{{__('text.select_type')}}</option>
                    <option value="1">{{__('text.tutiion_fee_only')}}</option>
                    <option value="2">{{__('text.partial_fee_only')}}</option>
                    <option value="3">{{__('text.partial_boarding_fee')}}</option>
                    <option value="4">{{__('text.boarding_fee_only')}}</option>
                    <option value="5">{{__('text.prompt1')}}</option>
                    <option value="6">{{__('text.full_time')}}</option>
                </select>
                @error('type')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-sm-1">
                <div class="d-flex justify-content-end">
                    <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>

                </div>
            </div>
        </div>
    </form>
</div>