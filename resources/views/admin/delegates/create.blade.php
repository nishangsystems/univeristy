@extends('admin.layout')
@section('section')
    <div class="py-3">
        <form method="post">
            @csrf
            <div class="row my-3 py-1">
                <label class="col-md-3 text-capitalize pl-4">@lang('text.word_year') :</label>
                <div class="col-md-9">
                    <select class="form-control rounded" name="year_id" required>
                        <option></option>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}" {{ old('year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row my-3 py-1">
                <label class="col-md-3 text-capitalize pl-4">@lang('text.word_campus') :</label>
                <div class="col-md-9">
                    <select class="form-control rounded" name="campus_id" required onchange="loadClasses(this)">
                        <option></option>
                        @foreach ($campuses as $campus)
                            <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row my-3 py-1">
                <label class="col-md-3 text-capitalize pl-4">@lang('text.word_class') :</label>
                <div class="col-md-9">
                    <select class="chosen-select form-control" name="class" id="class_selection_field" required  data-placeholder="search class by name...">
                        <option selected class="text-capitalize">{{__('text.select_class')}}</option>
                        @forelse(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                            <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end py-3 px-1" id="submit_box">
                <button class="btn btn-sm btn-primary rounded" type="submit">@lang('text.word_next')</button>
            <div>
        </form>
    </div>
@endsection
@section('script')
    <script>
        let loadClasses = function(selectElement){
            let campus = $(selectElement).val();
            let url = "{{ route('campus.program_levels', '__CID__') }}".replace('__CID__', campus);
            $.ajax({
                method: 'get', url: url, success: function(data){
                    let html = `<option></option>`;
                    console.log(data);
                    for (const key in data) {
                        let item = data[key];
                        console.log(item);
                        html += `<option value="${item.id}">${item.name}</option>`;
                    }
                    console.log(html);
                    $('#class_selection_field').html(html);
                    $('#submit_box').removeClass('hidden');
                }
            });
        }
    </script>
@endsection