@extends('parents.layout')
@section('section')
    @php
        
    @endphp
    <div class="card-body">
        <form method="post">
            <div class="input-group input-group-merge border border-seconary rounded">
                @csrf
                <select name="year" class="form-control border-0 border-left-1 border-right-1 rounded-left" id="" required>
                    <option value="">{{__('text.academic_year')}}</option>
                    @foreach(\App\Models\Batch::all() as $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                    @endforeach
                </select>
                <select name="semester" class="form-control border-0 border-left border-right rounded-left" id="" required>
                    <option value="">{{__('text.word_semester')}}</option>
                    @foreach(\App\Models\ProgramLevel::find($student->_class()->id)->program()->first()->background()->first()->semesters()->get() as $sem)
                        <option value="{{$sem->id}}">{{$sem->name}}</option>
                    @endforeach
                </select>
                <input type="submit" class="btn btn-primary btn-sm text-capitalize" value="{{__('text.word_get')}}">
            </div>
        </form>
    </div>
@endsection
