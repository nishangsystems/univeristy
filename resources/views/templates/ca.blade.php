<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{url('public/assets/css')}}/result.css" media="all" />
  </head>
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
            <th>CA mark(/30)</th>
          </tr>
        </thead>
        <tbody>
        @foreach($results as $result)
            <tr>
            <td>{{$result->course->course_code}}</td>
            <td>{{$result->course->title}}</td>
            <td>{{$result->ca}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </main>
  </body>
</html>