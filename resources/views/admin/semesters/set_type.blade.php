@extends('admin.layout')
@section('section')
<div class="py-4">
    <form action="{{route('admin.semesters.set_type', request('program_id'))}}" method="post">
        @csrf
        <div class="row py-3 text-capitalize">
            <input type="hidden" name="program_id" id="" value="{{request('program_id')}}">
            <label class="col-md-3">{{__('text.word_program')}}</label>
            <div class="col-md-9 col-lg-9">
                <input type="text" name="" class="form-control" disabled id="" value="{{\App\Models\SchoolUnits::find(request('program_id'))->name}}">
            </div>
        </div>
        <div class="row py-3 text-capitalize">
            <label for="" class="col-md-3">{{__('text.background')}}</label>
            <div class="col-md-9 col-lg-9">
                <select name="background_id" id="" class="form-control">
                    <option value="">{{__('text.background')}}</option>
                    @foreach($semester_types as $type)
                        <option value="{{$type->id}}">{{$type->background_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="py-3 d-flex justify-content-end">
            <input type="submit" value="{{__('text.word_save')}}" class="btn btn-primary btn-sm">
        </div>
    </form>
    <hr>
    <div>
        <table class="table">
            <thead>
                <th>###</th>
                <th>{{__('text.word_program')}}</th>
                <th>{{__('text.semester_type')}}</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\SchoolUnits::where('unit_id', 4)->get() as $program)
                    <tr>
                        <th>{{$k++}}</th>
                        <td>{{$program->name}} : <span class="text-primary">{{\App\Models\SchoolUnits::find($program->parent_id)->name}}</span></td>
                        <td>{{\App\Models\SemesterType::find($program->semester_type)->background_name ?? null}}</td>
                        <td><a href="{{route('admin.semesters.set_type', [$program->id])}}" class="btn btn-primary btn-sm">{{__('text.word_change')}}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection