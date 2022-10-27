@extends('admin.layout')
@section('section')
<div class="py-4">
    <form action="{{Request::url()}}" method="post">
        @csrf
        <div class="row my-2 text-capitalize">
            <label for="" class="col-md-3">{{__('text.select_program')}}</label>
            <div class="col-md-9 col-lg-9">
                <select name="program_id" class="form-control" id="" onchange="checkLevels(event.target)">
                    <option value="" selected>{{__('text.word_program')}}</option>
                    @foreach(\App\Models\SchoolUnits::where('unit_id', '4')->get() as $program)
                    <option value="{{$program->id}}">{{$program->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row my-2 text-capitalize">
            <label for="" class="col-md-3">{{__('text.word_levels')}}</label>
            <div class="col-md-9 col-lg-9 d-flex flex-wrap">
                @foreach(\App\Models\Level::all() as $level)
                <div class="my-3 mx-2 d-flex bg-light px-2 py-2 align-content-center">
                    <input type="checkbox" name="levels[]" class="checkbox" id="" value="{{$level->id}}">
                    <span class="text-secondary fs-4 mx-3">{{$level->level}}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="d-flex py-3 justify-content-end">
            <input type="submit" class="btn btn-sm btn-primary" value="assign" name="" id="">
        </div>
    </form>
</div>
@endsection
@section('script')
<!-- <script>
    function checkLevels(el){
        let val = el.value;
        url = "{{route('admin.programs.levels', ['__VAL__'])}}";
        url = url.replace('__VAL__', val);
        $.ajax({
            method: 'get',
            url: url,
            success: function(data){
                document.querySelectorAll(".checkbox").forEach(function(el, index, parent){
                    console.log(data.includes(el.value));
                    // el.setAttribute('checked', data.indexOf(el.value) != -1 ? true : false);
                })
            }
        })
    }
</script> -->
@endsection