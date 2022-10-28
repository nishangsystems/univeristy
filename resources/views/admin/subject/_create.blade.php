@extends('admin.layout')
@section('section')




    <?php
$level=\App\Models\Level::find(request('lid'));
 $prog=\App\Models\SemesterType::find(request('background'));
 $sem=\App\Models\Semester::find(request('semester'));
?>

<div class="alert alert-info">
<strong>Addind Subjects to {{$prog->background_name}} {{$sem->name}} Semseter  Level {{$level->level}}</strong> 
</div>




<form class="form-horizontal" id="block-validate" role="form" action="{{route('admin.subjects.store')}}" method="post" style="background:#fff; ">
 
 @csrf
  
        <div class="form-row">
         <div class="form-group col-md-1">
           <label for="inputEmail4">Level</label>
           <select class="form-control" id="sel1" name="level">
                <option value="{{$level->id}}">{{$level->level}}</option>
                
              </select>         </div>
         
         
         <div class="form-group col-md-3">
           <label for="inputPassword4">Programme</label>
           <select class="form-control" id="sel1" name="background">
                <option value="{{$prog->id}}">{{$prog->background_name}}</option>
                
              </select> 
        </div>
        
        <div class="form-group col-md-1">
                <label for="inputPassword4"> Semester</label>
                <select class="form-control" id="sel1" name="semester" required>
                        
                        <option value="{{$sem->id}}">{{$sem->name}}</option>
                           
                      </select>                           
         </div>

              
         <div class="form-group col-md-4">
                <label for="inputPassword4">Course Title</label>
                <input type="text" id="form-field-1" name="name" required placeholder="Course Title"class="form-control" />
                           
         </div>

         <div class="form-group col-md-2">
                <label for="inputPassword4"> Code</label>
                <input type="text" id="form-field-1" name="code" placeholder="Course Code"class="form-control" />
                           
         </div>

         

         <div class="form-group col-md-1">
                <label for="inputPassword4"> CV</label>
                <input type="text" id="form-field-1" name="coef" required  class="form-control" />
                           
         </div>


         <div class="form-group col-md-1">
                <label for="inputPassword4"> Status</label>
                <select class="form-control" id="sel1" name="status" required>
                        <option ></option>
                        <option value="C">C</option>
                        <option value="E">R</option>
                        <option value="R">G</option>

                        
                      </select>                           
         </div>


         

       </div>
       <div class="clearfix form-actions">
            <!--<div class="col-md-offset-3 col-md-3">-->
            <div class="col-md-3">
                <button class="btn btn-info" type="submit">
                    <i class="ace-icon fa fa-check bigger-110"></i>
                    Save
                </button>

              
            </div>
        </div>

</form>

    
   


    <!--<form action="{{route('admin.subjects.store')}}" method="post" class="text-capitalize">-->
    <!--    @csrf-->
    <!--    <input type="hidden" name="semester" id="" value="{{request('semester')}}}">-->
    <!--    <div class="row my-2">-->
    <!--        <label for="" class="col-md-3">{{__('text.word_title')}}</label>-->
    <!--        <div class="col-md-9 col-lg-9">-->
    <!--            <input type="text" name="name" id="" required class="form-control">-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="row my-2">-->
    <!--        <label for="" class="col-md-3">{{__('text.course_code')}}</label>-->
    <!--        <div class="col-md-9 col-lg-9">-->
    <!--            <input type="text" name="code" id="" required class="form-control">-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="row my-2">-->
    <!--        <label for="" class="col-md-3">{{__('text.credit_value')}}</label>-->
    <!--        <div class="col-md-9 col-lg-9">-->
    <!--            <input type="number" name="coef" id="" min="1" required class="form-control">-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="row my-2">-->
    <!--        <label for="" class="col-md-3">{{__('text.word_level')}}</label>-->
    <!--        <div class="col-md-9 col-lg-9">-->
    <!--            <select name="level" id="" required class="form-control">-->
    <!--                <option value="">{{__('text.select_level')}}</option>-->
    <!--                @foreach(\App\Models\Level::all() as $level)-->
    <!--                <option value="{{$level->id}}">{{$level->level}}</option>-->
    <!--                @endforeach-->
    <!--            </select>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="row my-2">-->
    <!--        <label for="" class="col-md-3">{{__('text.word_status')}}</label>-->
    <!--        <div class="col-md-9 col-lg-9">-->
    <!--            <select name="status" id="" required class="form-control text-uppercase">-->
    <!--                <option value="">{{__('text.select_status')}}</option>-->
    <!--                <option value="C">C</option>-->
    <!--                <option value="R">R</option>-->
    <!--                <option value="G">G</option>-->
    <!--            </select>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="d-flex justify-content-end my-2">-->
    <!--        <input type="submit" value="{{__('text.word_save')}}" class="btn btn-primary btn-sm">-->
    <!--    </div>-->
    <!--</form>-->
    <div class="py-4">
    <!--<hr>-->
    <div>
        <table class="table">
            <thead>
                <th>###</th>
                <th>{{__('text.word_title')}}</th>
                <th>{{__('text.course_code')}}</th>
                <th>{{__('text.credit_value')}}</th>
                <th>{{__('text.word_level')}}</th>
                <th>{{__('text.word_status')}}</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\Subjects::where('semester_id', request('semester'))->orderBy('updated_at', 'DESC')->get() as $subj)
                <tr>
                    <td>{{$k++}}</td>
                    <td>{{$subj->name}}</td>
                    <td>{{$subj->code}}</td>
                    <td>{{$subj->coef}}</td>
                    <td>{{\App\Models\Level::find($subj->level_id)->level}}</td>
                    <td>{{$subj->status}}</td>
                    <td>
                        <a href="{{route('admin.subjects.edit', $subj->id)}}" class="btn btn-primary btn-sm">{{__('text.word_edit')}}</a> | 
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection