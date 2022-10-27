@extends('admin.layout')
@section('section')
<div class="py-2">
    <div class="row">
        <form action="{{Request::url()}}" method="post" enctype="multipart/form-data" class="col-md-9 col-lg-9 px-2 py-1">
            @csrf
            <div class="row py-2">
                <label for="" class="col-md-2 fw-bold">{{__('text.word_year')}}</label>
                <div class="col-md-10 col-lg-10">
                    <select name="batch_id" id="" class="form-control" required>
                        <option value="" selected>{{__('text.academic_year')}}</option>
                        @foreach(\App\Models\Batch::all() as $batch)
                            <option value="{{$batch->id}}">{{$batch->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row py-2">
                <label for="" class="col-md-2 fw-bold">{{__('text.csv_file')}}</label>
                <div class="col-md-10 col-lg-10">
                    <input type="file" name="file" required id="" class="form_control">
                </div>
            </div>
            <div class="d-flex justify-content-end py-3">
                <input type="submit" value="submit" class="btn btn-primary btn-sm">
            </div>
        </form>
        <div class="col-md-3 col-lg-3 px-2 py-1">
            <h5 class="text-center text-capitalize">{{__('text.file_format_csv')}}</h5>
            <table style="background-color: #dedeff;">
                <thead class="text-capitalize" style="background-color: #dfdedf; border-bottom: 1px solid white;">
                    <th>{{__('text.word_matricule')}}</th>
                    <th>{{__('text.word_amount')}}</th>
                    <th>{{__('text.reference_number')}}</th>
                </thead>
                @for($i = 0; $i < 4; $i++)
                    <tr class="border-bottom">
                        <td class="border-left">FT23CR194</td>
                        <td class="border-left">800000</td>
                        <td class="border-left">rf32342d</td>
                    </tr>
                @endfor
            </table>
        </div>
    </div>
</div>
@endsection