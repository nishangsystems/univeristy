@extends('admin.layout')
@section('section')
<div class="py-3">
    <div class="row">
        <form action="{{Request::url()}}" method="post" class="col-md-9 col-lg-9" enctype="multipart/form-data">
            @csrf
            <div class="row py-2">
                <label for="" class="col-md-2">{{__('text.academic_year')}}</label>
                <div class="col-md-10 col-lg-10">
                    <select name="year" id="" class="form-control" required>
                        <option value="" selected>{{__('text.academic_year')}}</option>
                        @foreach(\App\Models\Batch::all() as $batch)
                         <option value="{{$batch->id}}">{{$batch->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row py-2">
                <label for="" class="col-md-2">{{__('text.word_semester')}}</label>
                <div class="col-md-10 col-lg-10">
                    <select name="semester" id="" class="form-control" required>
                        <option value="" selected>{{__('text.word_semester')}}</option>
                        @foreach(\App\Models\Semester::all() as $sem)
                         <option value="{{$sem->id}}">{{$sem->background()->first()->background_name.' >>> '.$sem->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row py-2">
                <label for="" class="col-md-2">{{__('text.word_file')}}</label>
                <div class="col-md-10 col-lg-10">
                    <input type="file" name="file" id="" class="form-control" required>
                </div>
            </div>
            <div class="row py-2">
                <label for="" class="col-md-2">{{__('text.word_reference')}}</label>
                <div class="col-md-10 col-lg-10">
                    <input type="text" name="reference" id="" class="form-control" required>
                </div>
            </div>
            <div class="d-flex justify-content-end py-2">
                <a href="{{url()->previous()}}" class="btn btn-sm btn-danger">{{__('text.word_cancel')}}</a> |
                <button type="submit" class="btn btn-sm btn-primary">{{__('text.word_save')}}</button>
            </div>
        </form>
        <div class="col-md-3 col-lg-3">
            <div class="text-center fw-bolder h4">{{__('text.file_format_csv')}}</div>
            <table>
                <thead class="bg-light">
                    <th class="border-left border-right">{{__('text.word_matricule')}}</th>
                    <th class="border-left border-right">{{__('text.course_code')}}</th>
                    <th class="border-left border-right">{{__('text.ca_mark')}}</th>
                </thead>
                <tbody>
                    @for($i = 0; $i < 4; $i++)
                        <tr class="border-bottom">
                            <td class="border-left border-right text-center">----</td>
                            <td class="border-left border-right text-center">----</td>
                            <td class="border-left border-right text-center">----</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection