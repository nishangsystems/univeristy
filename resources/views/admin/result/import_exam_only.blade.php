@extends('admin.layout')
@section('section')
<div class="py-3">
    <div class="row">
        <form action="{{Request::url()}}"  method="post" id="import_form" name="import_form" enctype="multipart/form-data" class="col-md-7 text-capitalize">
            @csrf
            <div class="my-2 row">
                <label for="" class="col-md-2">{{__('text.word_semester')}}</label>
                <div class="col-md-10">
                    <select name="semester_id" id="" class="form-control" required>
                        <option></option>
                        @foreach (\App\Models\ProgramLevel::find(request('class_id'))->program->background->semesters as $sem)
                            <option value="{{$sem->id}}">{{$sem->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="my-2 row">
                <label for="" class="col-md-2">{{__('text.word_file')}}</label>
                <div class="col-md-10">
                    <input type="file" name="file" id="" class="form-control" required accept="csv">
                </div>
            </div>
            <div class="my-2 row">
                <label for="" class="col-md-2">{{__('text.word_reference')}}</label>
                <div class="col-md-10">
                    <input type="text" name="reference" id="" class="form-control" required>
                </div>
            </div>
            <div id="actions" class="my-2 d-flex justify-content-end pr-0">
                <button class="btn btn-sm btn-primary" onclick="_fill()">{{__('text.word_save')}}</button>
            </div>
        </form>
        <div class="col-md-4 p-2">
            <div class="text-center text-capitalize">{{__('text.file_format_csv')}}</div>
            <table>
                <thead class="bg-dark text-light text-capitalize">
                    <th class="border-left border-right border-info">{{__('text.word_matricule')}}</th>
                    <th class="border-left border-right border-info">{{__('text.word_score')}}</th>
                </thead>
                <tbody>
                    @for($i = 0; $i < 4; $i++)
                    <tr class="bg-secondary text-light border-bottom border-light">
                        <td class="border-left border-right border-light">[ ------ ]</td>
                        <td class="border-left border-right border-light">[ ------ ]</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="my-3">
    @php
        $k = 1;
        $year_id = request()->has('year_id') ? request('year_id') : \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $class_id = request('class_id');
        $semester_id = request()->has('semester_id') ? request('semester_id') : \App\Helpers\Helpers::instance()->getSemester($class_id)->id;

        $results = \App\Models\OfflineResult::where(['batch_id'=>$year_id, 'class_id'=>$class_id, 'semester_id'=>$semester_id])->get();
    @endphp
    <table class="table"> 
        <thead class="bg-secondary text-light text-uppercase">
            <th class="border-left border-right border-white">#</th>
            <th class="border-left border-right border-white">{{__('text.word_matricule')}}</th>
            <th class="border-left border-right border-white">{{__('text.word_name')}}</th>
            <th class="border-left border-right border-white">{{__('text.ca_score')}}</th>
            <th class="border-left border-right border-white">{{__('text.exam_score')}}</th>
        </thead>
        <tbody class="bg-light">

            @foreach ($results as $result)
                <tr class="border-bottom border-secondary">
                    <td class="border-left border-right border-white">{{$k++}}</td>
                    <td class="border-left border-right border-white">{{$result->student->matric}}</td>
                    <td class="border-left border-right border-white">{{$result->student->name}}</td>
                    <td class="border-left border-right border-white">{{$result->ca_score}}</td>
                    <td class="border-left border-right border-white">{{$result->exam_score}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@section('script')
<script>
    
</script>
@endsection