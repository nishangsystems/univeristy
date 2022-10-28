@extends('student.layout.')

@section('section')
<div id="modal-table" class="modal fade" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<div class="table-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							<span class="white">&times;</span>
						</button>
						<div id="modal-dialog-title"></div>
					</div>
				</div>
				<div class="modal-body">
					<select id="level" required class="chosen-select" name="level">
						<option value="">Select Level</option>
						@foreach(\App\Level::get() as $level)
								<option value="{{$level->id}}">{{$level->name}}</option>
						@endforeach
					</select>
					<Button id="get_level" style="padding:5px; margin:10px; float:bottom;" class="btn-primary">
					<i class="spinner fa fa-spinner d-none" id="spinner"></i>
					View Courses</Button>
					<table class="table table-bordered">
						<thead>
							<tr>
							<th>Subject</th>
							<th>Code</th>
							<th>Credit Value</th>
							<th>Status</th>
							<th></th>
							</tr>
						</thead>
						<tbody id="unsigned-courses">
							
						</tbody>
					</table>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>                              
@if($medicals)

@if( \App\Config::all()->last()->courseValid())
<div class="row">
        <div >
		<h3>My Level {{\Auth::guard('student')->user()->studentInfo->level->name}}  {{\App\Semester::find( \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate')))->name}} Signed Courses</h3>
			
			<form id="register-form" class="panel-body">
			{{csrf_field()}}	
			<input type="hidden" name="level" value="{{\Auth::guard('student')->user()->studentInfo->level->name}}">
			<a  href="#modal-table" data-toggle="modal" style="padding:5px; margin:5px; float:left;" class="btn-primary"><i class="fa fa-plus"></i>&nbsp;&nbsp;Sign Course</a>   
			
			<input type="hidden" name="std_id" value="{{\Auth::guard('student')->user()->id}}">	
				<table class="table table-bordered">
					<thead>
					<tr>
						<th>#</th>
						<th>Subject</th>
						<th>Code</th>
						<th>Credit Value</th>
						<th>Status</th>
						<th></th>
					</tr>
					</thead>
					<tbody id="registered">
					<?php $count = 1; ?>
						@foreach($courses as $course)
							<input type="hidden" name="ids[]" value="{{$course->id}}">
							<tr>
								<td>{{$count}}</td>
								<td>{{$course->byLocale()->title}}</td>
								<td>{{$course->course_code}}</td>
								<td>{{$course->credit_value}}</td>
								<td>{{$course->status}}</td>
								<td>
									<span onclick="dropCourse('{!!$course->id!!}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></i>&nbsp;&nbsp;Drop</span></td>
							</tr>
							<?php $count = $count+1; ?>
						@endforeach
							
					</tbody>
				</table>
				<Button type="submit" style="padding:5px; margin:5px;" class="btn btn-success"><i class="spinner fa fa-spinner d-none" id="form-spinner"></i>&nbsp;&nbsp;<i class="fa fa-save"></i>&nbsp;&nbsp;Save</Button> 
				@if($courses->count() > 0)
				<a style="padding:5px; margin:5px;" class="btn btn-primary" href="{{route('courses.download',[\App\Semester::find( \App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate')))->id,
					 \App\Semester::find( \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate')))->id])}}"><i class="fa fa-download"></i>&nbsp;&nbsp;Download Form B/ Form R</a>   
				@endif  
				<label style="padding:5px; margin:5px;" id="credit-value" class="btn btn-primary" >&nbsp;&nbsp;</label> 
					</form>
  		</div>
</div> 

@else

<div class="row">
        <div>
			<h3>My Level {{\Auth::guard('student')->user()->studentInfo->level->name}}  {{\App\Semester::find( \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate')))->name}} Signed Courses</h3>
			
		  </div>
		  <div class="alert alert-danger fade in">
				<h5>Course Registration period closed</h5>
		 </div>
</div> 

@endif

@else

<div class="row">
        <div>
			<h3>My Level {{\Auth::guard('student')->user()->studentInfo->level->name}}  {{\App\Semester::find( \App\Helpers\Helpers::instance()->getCurrentSemester(Session::get('graduate')))->name}} Signed Courses</h3>
			
		  </div>
		  <div class="alert alert-danger fade in">
				<h5>{{\Auth::guard('student')->user()->studentInfo->firstname}}, Has Not Done Medicals for  {{\App\Year::find( \App\Helpers\Helpers::instance()->getCurrentAccademicYear(Session::get('graduate')))->name}}</h5>
		 </div>
</div> 


@endif

@section('script')		   
<script>
	let registered = '{!!$courses!!}'
	let credit = '{!!$credit!!}'
	let maxCredit = "{!!\Auth::guard('student')->user()->studentInfo->options->max_credit!!}"
	$('#credit-value').html("Credit Value : "+credit+" / "+maxCredit);
	console.log( maxCredit)
	sessionStorage.setItem('registered',registered);
	sessionStorage.setItem('totalCredit',credit);
	sessionStorage.setItem('maxCredit',maxCredit);

	function setLevelCourses(level) {
			console.log(level)
            $.ajax({
                type: "POST",
                url: "{{route('api.student.levelcourses')}}",
				dataType: 'JSON',
                data: {
                    '_token': "{!! csrf_token() !!}",
					'level':level,
					'program':"{{\Auth::guard('student')->user()->studentInfo->program_id}}"
                },
                success: function (data) {
					console.log(data);
					max_credit = data.max_credit;
					courses = data.courses;
					let html = "";
					sessionStorage.setItem('unRegistered',JSON.stringify(courses));
                    for (let i = 0; i < courses.length; i++) {
						course = courses[i];
                      if(!hasRegisteredCourse(course.id)){
						html += "<tr>"+
									"<td>"+course.title+"</td>"+
									"<td>"+course.course_code+"</td>"+
									"<td>"+course.credit_value+"</td>"+
									"<td>"+course.status+"</td>"+
									"<td>"+
									"<span class='btn btn-sm btn-primary' onclick='addCourse("+
									JSON.stringify(course)+
									")'><i class='fa fa-plus'></i></i>&nbsp;&nbsp;Add</span></td>"+
                 				"</tr>";
					  }
				    }
					toastr.info("Done");
					$('#modal-dialog-title').html(data.title);
                    $('#unsigned-courses').html(html);
					$('#spinner').addClass('d-none');
                },
				error: function(e){
					console.log(e);
					$('#spinner').addClass('d-none');
				}
            });
        }

	setLevelCourses("{!!\Auth::guard('student')->user()->studentInfo->level->id!!}"); 
	if($('#level').val() != ''){
				$('#get_level').attr('disabled', false);
			}else{
				$('#get_level').attr('disabled', true);
			}
	 $(function () {
		 $('#level').on('change', function(){
			const level = $(this);
			if(level.val() != ''){
				$('#get_level').attr('disabled', false);
			}else{
				$('#get_level').attr('disabled', true);
			}
		 });
        $('#get_level').on('click', function () {
            const level = $("#level");
			setLevelCourses(level.val());
        });
    });

	function refresh(){

		unRegistered = JSON.parse(sessionStorage.getItem('unRegistered'));
		let uhtml = ""
		for (let i = 0; i < unRegistered.length; i++) {
				uc = unRegistered[i];
				if(!hasRegisteredCourse(uc.id)){
				uhtml += "<tr>"+
							"<td>"+uc.title+"</td>"+
							"<td>"+uc.course_code+"</td>"+
							"<td>"+uc.credit_value+"</td>"+
							"<td>"+uc.status+"</td>"+
							"<td>"+
							"<span class='btn btn-sm btn-primary' onclick='addCourse("+
							JSON.stringify(uc)+
							")'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add</span></td>"+
						"</tr>";
				}
			}
		$('#unsigned-courses').html(uhtml);
		let html = ""
		registered = JSON.parse(sessionStorage.getItem('registered'));
		let credit_v = 0;
		for (let i = 0; i < registered.length; i++) {
				rc = registered[i];
				credit_v = credit_v + rc.credit_value
				
				html += "<input type='hidden' name='ids[]' value='"+rc.id+"'>"+
							"<tr>"+
							"<td>"+(parseInt(i+1))+"</td>"+
           
							"<td>"+rc.title+"</td>"+
							"<td>"+rc.course_code+"</td>"+
							"<td>"+rc.credit_value+"</td>"+
							"<td>"+rc.status+"</td>"+
							"<td>"+
								"<span class='btn btn-sm btn-danger' onclick=dropCourse('"+
								rc.id
								+"')><i class='fa fa-trash'></i>&nbsp;&nbsp;Drop</span>"+
							"</td>"+
						"</tr>";
				}
		$('#registered').html(html);
		$('#credit-value').html("Credit Value : "+credit_v+" / "+maxCredit);
		sessionStorage.setItem('totalCredit',credit_v);
		
		toastr.success("Done");
	}

	$('#register-form').submit(function (event) {
			$('#form-spinner').removeClass('d-none');
            event.preventDefault();
            $('#register-form').removeClass('d-none');
            const form = $('#register-form');
			ids = form.find('input[name=ids]').val();
			console.log($('#register-form').serialize())
			$.ajax({
                type: "POST",
                url: "{{route('api.student.save')}}",
				dataType: 'JSON',
                data: $('#register-form').serialize(),
                success: function (data) {
					toastr.info("Your Course Details have being saved Successfully");
					$('#form-spinner').addClass('d-none');
                },
				error: function(){
					$('#form-spinner').addClass('d-none');
				}
            });

        });

	function addCourse(course){
		if((parseInt(sessionStorage.getItem('totalCredit')) + parseInt(course.credit_value)) > sessionStorage.getItem('maxCredit')){
			console.log((parseInt(sessionStorage.getItem('totalCredit')) + parseInt(course.credit_value)))
			console.log(sessionStorage.getItem('maxCredit'))
				toastr.error("Your Credit Value will be exceeded", "Can't Add Course!!");
			}else{
				if(!hasRegisteredCourse(course.id)){
					let cs = JSON.parse(sessionStorage.getItem('registered'));
					cs.push(course);
					credit = parseInt(sessionStorage.getItem('totalCredit')) + parseInt(course.credit_value)
					$('#credit-value').html("Credit Value : "+credit+" / "+maxCredit);
					sessionStorage.setItem('totalCredit',credit);
					sessionStorage.setItem('registered',JSON.stringify(cs));
					refresh();
				}
			}
		
		}

		function dropCourse(course){
			let cs = JSON.parse(sessionStorage.getItem('registered'));
			const index = cs.findIndex(x => x.id === course);
			if (index !== undefined) cs.splice(index, 1);
			sessionStorage.setItem('registered',JSON.stringify(cs));
			refresh();
		}
	function hasRegisteredCourse(course_id){
		let cs = JSON.parse(sessionStorage.getItem('registered'));
		let exist = false;
		for (let i = 0; i < cs.length; i++) {
			let c = cs[i];
			if(c.id == course_id){
               exist = true;       
			}
		}
		return exist;
	}	

</script>
@endsection
            

@stop