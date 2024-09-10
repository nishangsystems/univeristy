<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{env('APP_NAME')}}</title>
    <style>
        #error_box{
            padding: 2.3rem 1.8rem !important;
            border: 1px solid black;
            background-color: rgb(128, 94, 6);
            color: white;
        }
        #error_box > strong{
            color: white;
            margin-right: 1.2rem;
        }
        #message_box{
            padding: 2.3rem 1.8rem !important;
            border: 1px solid black;
            background-color: rgb(128, 94, 6);
            color: white;
        }
        #message_box > strong{
            color: white;
            margin-right: 1.2rem;
        }
        #success_box{
            padding: 2.3rem 1.8rem !important;
            border: 1px solid black;
            background-color: rgb(128, 94, 6);
            color: white;
        }
        #success_box > strong{
            color: white;
            margin-right: 1.2rem;
        }
    </style>
</head>
<body>
    <div id="success_box">
        <b class="text-capitalize">{{$student->name??''}}</b><br>
        <strong class="text-capitalize">{{$status}}!</strong><span>{{$message}}</span>
    </div>
</body>
</html>