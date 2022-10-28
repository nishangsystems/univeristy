@extends('admin.layout')

@section('section')
<!-- page start-->

<div class="col-sm-12">
    <form method="post">
        @csrf
        <div class="flex-nowrap d-flex justify-content-between my-4">
            <a href="{{route('admin.units.subjects',[$parent->id, request('level_id')])}}" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Cancel</a>
            <input id="searchbox" class="flex-grow-1 mx-4 border px-5" placeholder="Type here to search" name="search">
            <button class="btn btn-sm btn-primary">Save</button>
        </div>

        <div class="row py-5">
            @foreach(\App\Models\Subjects::orderBy('name')->get() as $subject)
            <div class="form-group col-md-3" data-role="subjects">
                <div class="custom-control custom-checkbox">
                    <input {{$parent->subjects->contains($subject)?'checked':''}} type="checkbox" class="custom-control-input toggle" value="{{$subject->id}}" name="subjects[]" id="subject{{$subject->id}}">
                    <label class="custom-control-label px-5 font-weight-normal" for="subject{{$subject->id}}">
                       <span class="text-secondary">{{$subject->code}}</span> : {{$subject->name}}
                    </label>
                </div>
            </div>
            @endforeach
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $("#searchbox").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $('div[data-role="subjects"]').filter(function() {
                $(this).toggle($(this).find('.custom-control-label').text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection