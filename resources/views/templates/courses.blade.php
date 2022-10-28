<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{url('public/assets/css')}}/result.css" media="all" />
  </head>
  <style>
         tr,td,table,th{
             
             border:1px solid#000;
             border-collapse:collapse;
         } 
         .left{
             float:left;
             margin-left:40px;
             margin-top:30px;
             height:20px;
             padding:10px 10px;
             text-align:center;
         }
           .right{
             float:right;
             margin-top:25px;
             margin-right:40px;
             height:20px;
             padding:10px 10px;
             text-align:center;
         }
         .credit{
             width:90%;
             margin:auto;
             text-align:center;
         }
          
      </style>
  <body>
      
  <img class="image_fluid" src="{{url('public/assets/images')}}/header.jpg">
    <div class="container">
            <div>Name : &nbsp;&nbsp; {{$student->studentInfo->firstname}}  {{$student->studentInfo->lastname}}</div>
            <div>Matricule : &nbsp;&nbsp;{{$student->matric}}</div>
            <div>Department : &nbsp;&nbsp;{{$student->studentInfo->options->department->name}}</div>
            <div>Program : &nbsp;&nbsp; {{$student->studentInfo->options->name}}</div>
            <div>Year : &nbsp;&nbsp; {{$year->name}}</div>
            <div>Semester : &nbsp;&nbsp;{{$semester->name}}</div>
        </div>


    <main class="pplr">
      <div class="title">{{$title}}
      </div>
      <table>
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Status</th>
            <th>Credit Value</th>
            <th>Lecturer's<br>Signature</th>
          </tr>
        </thead>
        <tbody>
        @foreach($courses as $course)
        <tr>
            <td>{{$course->course_code}}</td>
            <td style="width:350px">{{$course->title}}</td>
            <td>{{$course->status}}</td>
            <td>{{$course->credit_value}}</td>
             
             <td></td>
          </tr>

        @endforeach
         
         
        </tbody>
      </table>
      <div class="credit">Total Credit Attempted : {{$student->credit($year->id, $semester->id)}}</div>
      <div class="left">_______________________<br>Student</div>
       <div class="right">_______________________<br>   Head of Department                        </div>
    </main>
  </body>
</html>