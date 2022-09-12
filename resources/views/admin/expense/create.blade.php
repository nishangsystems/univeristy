 <div class="form-panel">
     <form class="form-horizontal" role="form" method="POST" action="{{route('admin.expense.store')}}">

         @csrf
         <div class="row">
             <div class="col-sm-1">
                 <label for="cname" class="control-label  text-capitalize">{{__('text.word_name')}} <span style="color:red">*</span></label>
             </div>
             <div class="form-group @error('name') has-error @enderror col-sm-2">
                 <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
                 @error('name')
                 <span class="invalid-feedback">{{ $message }}</span>
                 @enderror
             </div>
             <div class="col-sm-2 d-flex justify-content-sm-center">
                 <label for="cname" class="control-label text-capitalize">{{__('text.amount_spent')}} <span style="color:red">*</span></label>
             </div>
             <div class="form-group @error('amount_spend') has-error @enderror col-sm-2">
                 <input class=" form-control" name="amount_spend" value="{{old('amount_spend')}}" type="number" required />
                 @error('amount_spend')
                 <span class="invalid-feedback">{{ $message }}</span>
                 @enderror
             </div>

             <div class="col-sm-2 d-flex justify-content-sm-center">
                 <label for="cname" class="control-label text-capitalize">{{__('text.expense_date')}} <span style="color:red">*</span></label>
             </div>
             <div class="form-group @error('date') has-error @enderror col-md-2">
                 <input class=" form-control" name="date" value="{{old('date')}}" type="date" required />
                 @error('date')
                 <span class="invalid-feedback">{{ $message }}</span>
                 @enderror
             </div>
             <div class="form-group col-sm-1">
                 <div class="d-flex justify-content-end">
                     <button id="save" class="btn btn-xs btn-primary mx-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>
                 </div>
             </div>
         </div>
     </form>
 </div>