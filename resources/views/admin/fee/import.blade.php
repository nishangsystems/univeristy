@extends('admin.layout')
@section('section')
<div class="py-2">
    <div class="row">
        <div  class="col-md-9 col-lg-9 px-2 py-1 ">
            <form action="{{Request::url()}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row py-2">
                    <label for="" class="col-md-2 fw-bold text-capitalize">{{__('text.word_year')}}</label>
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
                    <label for="" class="col-md-2 fw-bold text-capitalize">{{__('text.import_reference')}}</label>
                    <div class="col-md-10 col-lg-10">
                        <input type="text" name="import_reference" id="" class="form-control" required>
                    </div>
                </div>
                <div class="row py-2">
                    <label for="" class="col-md-2 fw-bold text-capitalize">{{__('text.csv_file')}}</label>
                    <div class="col-md-10 col-lg-10">
                        <input type="file" name="file" required id="" class="form_control">
                    </div>
                </div>
                <div class="d-flex justify-content-end py-3">
                    <input type="submit" value="submit" class="btn btn-primary btn-sm">
                </div>
            </form>

            <div>
                <button class="w-100 btn btn-md btn-success text-capitalize" onclick="toggle_show_imports()">{{__('text.word_imports')}}</button>
                <div class="py-2 hidden" id="import_list">
                    <table class="table">
                        <thead class="text-capitalize">
                            <th>###</th>
                            <th>{{__('text.import_reference')}}</th>
                            <th>{{__('text.word_date')}}</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @php($k = 1)
                            @foreach(array_unique(\App\Models\Payments::whereNotNull('import_reference')->distinct()->pluck('import_reference')->toArray()) as $import_ref)
                                @php($import = \App\Models\Payments::where('import_reference', $import_ref)->first())
                                @php(dd($import_ref))
                                <tr class="border-bottom border-dark">
                                    <td class="border-left border-right border-secondary">{{$k++}}</td>
                                    <td class="border-left border-right border-secondary">{{$import->import_reference ?? ''}}</td>
                                    <td class="border-left border-right border-secondary">{{date('l d-m-Y H:m', strtotime($import->created_at))}}</td>
                                    <td class="border-left border-right border-secondary">
                                        <a href="{{route('admin.fee.import.undo', $import_ref)}}" class="btn btn-sm btn-danger">{{__('text.word_undo')}}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
@section('script')
<script>
    function toggle_show_imports(){
        $('#import_list').toggleClass('hidden');
    }
</script>
@endsection