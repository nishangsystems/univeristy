    @extends('admin.layout')
    @section('section')
        <form method="post" target="_new">
            @csrf
            <div class="row my-3 py-3 text-capitalize">
                <div class=" col-sm-6 col-md-5 col-lg-5 px-2">
                    <label for="">{{__('text.word_class')}}</label>
                    <div>
                        <select name="class_id" id="" class="form-control rounded" required>
                            <option value=""></option>
                            @foreach(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                                <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class=" col-sm-6 col-md-4 col-lg-4 px-2">
                    <label for="">{{__('text.word_semester')}}</label>
                    <div>
                        <select name="semester_id" id="" class="form-control rounded" required>
                            <option value=""></option>
                            @foreach(\App\Models\Semester::all() as $sem)
                                <option value="{{$sem->id}}">{{$sem->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class=" col-sm-6 col-md-3 col-lg-3 px-2">
                    <label for="">{{__('text.word_year')}}</label>
                    <div>
                        <select name="year_id" id="" class="form-control rounded" required>
                            <option value=""></option>
                            @foreach(\App\Models\Batch::all() as $year)
                                <option value="{{$year->id}}">{{$year->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="my-2 px-0 mx-0 d-flex justify-content-end"><input type="submit" class="btn btn-sm text-capitalize btn-primary rounded" value="{{__('text.word_generate')}}"></div>
        </form>
    @endsection
