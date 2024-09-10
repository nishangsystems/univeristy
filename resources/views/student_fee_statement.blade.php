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
            border: 1px solid rgba(234, 99, 99, 0.9);
            background-color: rgba(246, 225, 225, 0.9);
            color: rgba(234, 99, 99, 0.9);
        }
        #error_box > strong{
            color: orangered;
            margin-right: 1.2rem;
        }
        #message_box{
            padding: 2.3rem 1.8rem !important;
            border: 1px solid rgba(99, 149, 234, 0.9);
            background-color: rgba(246, 225, 225, 0.9);
            color: rgba(91, 160, 217, 0.9);
        }
        #message_box > strong{
            color: rgb(46, 32, 239);
            margin-right: 1.2rem;
        }
        #success_box{
            padding: 2.3rem 1.8rem !important;
            border: 1px solid rgba(51, 202, 157, 0.9);
            background-color: rgba(246, 225, 225, 0.9);
            color: rgba(92, 239, 193, 0.9);
        }
        #success_box > strong{
            color: rgb(31, 233, 109);
            margin-right: 1.2rem;
        }
    </style>
</head>
<body>
    @if ($status == 'error')
        <div id="error_box"><strong>Error!</strong><span>{{$message}}</span></div>
    @elseif ($status == 'message')
        <div id="message_box"><strong>Message!</strong><span>{{$message}}</span></div>
    @else
        <div id="success_box"><strong>Success!</strong><span>{{$message}}</span></div>
    @endif
</body>
</html>