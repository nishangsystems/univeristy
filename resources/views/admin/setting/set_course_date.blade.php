@extends('admin.layout')
@section('section')
<div class="py-3">
    <form method="get">
        <!-- @csrf -->
        <div class="input-group border border-secondary rounded my-3 text-capitalize">
            <span class="input-group-text bg-secondary text-light fw-bolder col-sm-3 col-md-2">{{__('text.word_background')}}</span>
            <select name="background" id="" class="form-control text-uppercase" required>
                <option value=""></option>
                @foreach(\App\Models\Background::all() as $bg)
                    <option value="{{$bg->id}}" {{request('background') == $bg->id ? 'selected' : ''}}>{{$bg->background_name}}</option>
                @endforeach
            </select>
            <input type="submit" value="{{__('text.word_semesters')}}">
        </div>
    </form>

    <div class="text-center alert-info py-1 hidden" id="alert">
        <span class="tetx-primary py-1 text-capitalize mx-4">{{__('text.word_semester')}} : <strong id="current_semester"></strong></span>
        <span class="tetx-success py-1 text-capitalize mx-4">{{__('text.date_line')}} : <strong id="date_line"></strong></span>
    </div>
    
    @if(request()->has('background'))
    <form method="post" class=" py-5 text-capitalize">
        @csrf
        <div class="input-group border border-secondary rounded my-3">
            <span class="input-group-text bg-light text-dark fw-bolder col-sm-3 col-md-2">{{__('text.word_semester')}}</span>
            <select name="semester" id="semester" class="form-control text-uppercase" required onchange="loadDate(event.target)">
                <option value=""></option>
                @foreach(\App\Models\Semester::where(['background_id'=>request('background')])->get() as $bg)
                    <option value="{{$bg->id}}">{{$bg->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group border border-secondary rounded my-3">
            <span class="input-group-text bg-light text-dark fw-bolder col-sm-3 col-md-2">{{__('text.word_date')}}</span>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <div class="d-flex justify-content-end my-3">
            <input type="submit" name="" id="" class="btn btn-primary btn-sm" value="{{__('text.word_save')}}">
        </div>
    </form>
    @endif
</div>
@endsection
@section('script')
<script>
    $(document).ready(function name(params) {
        loadDate();
    })
    function loadDate(el=null) {
        sem = el==null ? "{{$current_semester ?? null}}" : $(el).val();
        campus = "{{auth()->user()->campus_id ?? null}}";
        if (sem == null || campus == null) {
            return;
        }
        console.log(campus+' '+sem);
        url = "{{route('admin.courses.registration.date_line', ['__C__', '__S__'])}}";
        url = url.replace('__C__', campus);
        url = url.replace('__S__', sem);
        $.ajax({
            method: 'get',
            url: url,
            success:function(data){
                console.log(data);
                $('#current_semester').text(data.semester);
                $('#date_line').text(data.date_line);
                $('#date').val(data.date);
                $('#semester').val(sem);
                $('#alert').removeClass('hidden');
            }
        })
    }
</script>
@endsection