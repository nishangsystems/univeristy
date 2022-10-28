@extends('admin.layout')

@section('section')
<div class="alert alert-info">
        <strong>All Our Levels</strong> 
    </div>
    
    
    
<form id="hdTutoForm" method="POST" action="{{route('admin.courses.create_next')}}">

@csrf
                 
                <div class="col-sm-3">
                 <label for="inputEmail4">Level</label>
                     <select class="form-control" id="sel1" name="lid" required>
                    <option></option>
                    <?php $periods=\App\Models\Level::orderby('id' )->get();
                        foreach ($periods as $period) {
                         
                    ?>
                        <option value="{{$period->id}}">{{$period->level}}</option>
                        <?php } ?>
        
                    
                    </select>
                </div>
                
                <div class="col-sm-3">
                 <label for="inputEmail4">Text.Word_background</label>
                     <select class="form-control" id="sel1" name="background" required onchange="loadSemesters(event.target)">
                    <option></option>
                    <?php $backg=\App\Models\SemesterType::all();
                        foreach ($backg as $bgs) {
                         
                    ?>
                        <option value="{{$bgs->id}}">{{$bgs->background_name}}</option>
                        <?php } ?>
                </select>
                </div>
                
               <div class="col-sm-3">
                <div class="input-gpfrm input-gpfrm-lg" id="semesters-box">
                    <label for="inputEmail4">Semester: </label>
                    <div class="col-lg-10" id="semesters">
                        <input class=" form-control" name="coef" value="{{old('coef')}}" type="number" required  disabled/>
                    </div>
                    </div>
                 </div>
                 
                  <!--                <div class="form-group @error('coef') has-error @enderror" id="semesters-box">-->
<!--                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_semester')}} ({{__('text.word_required')}})</label>-->
<!--                    <div class="col-lg-10" id="semesters">-->
<!--                        <input class=" form-control" name="coef" value="{{old('coef')}}" type="number" required  disabled/>-->
<!--                    </div>-->
<!--                </div>-->
    
    
    
    <div class="form-group col-md-3"><br>
      <input type="submit" class="next btn btn-primary" value="NEXT" name="oks" /> 
    </div>
    
    
     </div>
     </fieldset>
     </form>
    
    </div>
@stop


<!--@section('section')-->
<!--     FORM VALIDATION -->
<!--    <div class="mx-3">-->
<!--        <div class="form-panel">-->
<!--            <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.courses.create_next')}}">-->
<!--                {{csrf_field()}}-->
<!--                <div class="form-group @error('name') has-error @enderror">-->
<!--                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_backgroun')}} ({{__('text.word_required')}})</label>-->
<!--                    <div class="col-lg-10">-->
<!--                        <select class=" form-control" name="background" required onchange="loadSemesters(event.target)">-->
<!--                            <option value="">{{__('text.select_background')}}</option>-->
<!--                            @foreach(\App\Models\SemesterType::all() as $bgs)-->
<!--                                <option value="{{$bgs->id}}">{{$bgs->background_name}}</option>-->
<!--                            @endforeach-->
<!--                        </select>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="form-group @error('coef') has-error @enderror" id="semesters-box">-->
<!--                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_semester')}} ({{__('text.word_required')}})</label>-->
<!--                    <div class="col-lg-10" id="semesters">-->
<!--                        <input class=" form-control" name="coef" value="{{old('coef')}}" type="number" required  disabled/>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="form-group">-->
<!--                    <div class="col-lg-offset-2 col-lg-10 text-capitalize">-->
<!--                        <button class="btn btn-xs btn-primary" type="submit">{{__('text.word_save')}}</button>-->
<!--                        <a class="btn btn-xs btn-danger" href="{{route('admin.subjects.index')}}" type="button">{{__('text.word_cancel')}}</a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
<!--@endsection-->
<!--@section('script')-->
<script>
    function loadSemesters(element){
        let bg = element.value;
        let url = "{{route('semesters', '__BG')}}";
        url = url.replace('__BG', bg);
        $.ajax({
            method:'get',
            url: url,
            success: function(data){
                console.log(data);
                let semester_ = `<select name="semester" class="form-control" required>`;
                data.forEach(element => {
                    semester_ = semester_+`<option value="`+element.id+`">`+element.name+`</option>`;
                });
                semester_ = semester_+`</select>`;
                $('#semesters').html(semester_);
            }
        })
    }
</script>
@endsection
