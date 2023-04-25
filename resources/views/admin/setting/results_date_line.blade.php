@extends('admin.layout')
@section('section')
<div class="py-3">
    <form method="get">
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
        <span class="tetx-success py-1 text-capitalize mx-4">{{__('text.ca_dateline')}} : <strong id="ca_date_line"></strong></span>
        <span class="tetx-success py-1 text-capitalize mx-4">{{__('text.exam_dateline')}} : <strong id="exam_date_line"></strong></span>
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
            <span class="input-group-text bg-light text-dark fw-bolder col-sm-3 col-md-2">{{__('text.ca_dateline')}}</span>
            <input type="date" name="ca_latest_date" id="date" class="form-control" required>
        </div>
        <div class="input-group border border-secondary rounded my-3">
            <span class="input-group-text bg-light text-dark fw-bolder col-sm-3 col-md-2">{{__('text.exam_dateline')}}</span>
            <input type="date" name="exam_latest_date" id="date" class="form-control">
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
    $(document).ready(loadDate())
    function loadDate(el=null) {
        sem = el==null ? "{{$current_semester ?? null}}" : $(el).val();
        campus = "{{auth()->user()->campus_id ?? null}}";
        if (sem == null || campus == null) {
            return;
        }
        console.log(campus+' '+sem);
        url = "{{route('admin.results.get_dateline')}}";
        $.ajax({
            method: 'get',
            url: url,
            data: {'campus_id' : campus, 'semester_id' : sem},
            success:function(data){
                console.log(data);
                $('#current_semester').text(data.semester);
                $('#ca_date_line').text(data.ca_dateline);
                $('#exam_date_line').text(data.exam_dateline);
                $('#date').val(data.date);
                $('#semester').val(data.semester_id);
                $('#alert').removeClass('hidden');
            }
        })
    }
</script>
@endsection