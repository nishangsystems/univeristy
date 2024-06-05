<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal">

            <div class="form-group  mt-5">
                <label class="control-label col-lg-2 text-capitalize">Student Name</label>
                <div class="col-lg-10">
                    <input type="text" class="form-control" name="name" wire:model="name" readonly>
                </div>
            </div>

            <div class="form-group  mt-3">
                <label class="control-label col-lg-2 text-capitalize">Batch <span
                            style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" wire:model="year">
                        <option value="">{{__('text.select_year')}}</option>
                        @foreach(\App\Models\Batch::all() as $key => $year)
                            <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group mt-3">
                <label class="control-label col-lg-2 text-capitalize">HND Results <span
                            style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class="form-control" wire:model="grade">
                        <option value="">Select Result</option>
                        @foreach(\App\Models\Grading::where('grading_type_id', 5)->orderBy('grade')->get() as $key => $grade)
                            <option value="{{$grade->id}}">{{$grade->grade}}</option>
                        @endforeach
                    </select>
                    @error('grade')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-end align-items-center col-lg-12">
                    <button type="button" wire:click="save" class="btn btn-xs px-4 py-2 btn-primary mr-4" wire:loading.attribute = 'disabled'>
                        <i class="fa fa-spinner d-none" wire:loading.class.remove="d-none" wire:target="save"></i>
                        {{__('text.word_save')}}
                    </button>
                    <a class="btn btn-xs btn-danger " href="{{route('admin.transcript.index')}}">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>
    </div>


    @if(isset($result))
       <div style="width: 300px;">
           <table >
               <thead>
               <tr>
                   <th style="width: 100px;">Year</th>
                   <th style="width: 100px">Grade</th>
                   <th style="width: 100px">Grade Point</th>
               </tr>
               </thead>
               <tbody>
               <tr>
                   <td> {{$result->year->name}}</td>

                   @php(  $grade = \App\Models\Grading::find($result->exam_score))
                   <td>{{($grade != "") ? $grade->grade : "-"}}</td>
                   <td>{{isset($grade) ? $grade->weight : 0.0}}</td>
               </tr>
               </tbody>
           </table>
       </div>


        <button type="button" wire:click="delete" class="btn btn-xs px-4 py-2 btn-danger" wire:loading.attribute = 'disabled'>
            <i class="fa fa-spinner d-none" wire:loading.class.remove="d-none" wire:target="delete"></i>
            Delete Result
        </button>
    @endif
</div>