@extends('admin.layout')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="mx-3">
        <div class="row">
            <div class="col-md-8 col-xl-8 py-3 card">
                <div class="form-panel card-body">
                    <form class="cmxform form-horizontal style-form" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group @error('name') has-error @enderror">
                            <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_background')}} ({{__('text.word_required')}})</label>
                            <div class="col-lg-10">
                                <select class=" form-control" name="background" required onchange="loadSemesters(event.target)">
                                    <option value="">{{__('text.select_background')}}</option>
                                    @foreach(\App\Models\SemesterType::all() as $bgs)
                                        <option value="{{$bgs->id}}">{{$bgs->background_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group @error('semester') has-error @enderror" id="semesters-box">
                            <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_semester')}} ({{__('text.word_required')}})</label>
                            <div class="col-lg-10" id="semesters">
                                <input class=" form-control" name="semester" type="number" required  disabled/>
                            </div>
                        </div>
                        
                        <div class="form-group @error('file') has-error @enderror" id="semesters-box">
                            <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_semester')}} ({{__('text.word_required')}})</label>
                            <div class="col-lg-10" id="semesters">
                                <input class=" form-control" name="file" type="file" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10 text-capitalize">
                                <button class="btn btn-xs btn-primary" type="submit">{{__('text.word_save')}}</button>
                                <a class="btn btn-xs btn-danger" href="{{route('admin.subjects.index')}}" type="button">{{__('text.word_cancel')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4 col-xl-4 py-3 card">
                <div class="card-body">
                    <table class="table-dark border rounded">
                        <thead class="text-capitalize">
                            <tr class="border-top border-bottom">
                                <td colspan="4" class="heading">@lang('text.file_format_csv')</td>
                            </tr>
                            <tr class="border-top border-bottom">
                                <th>@lang('text.course_code')</th>
                                <th>@lang('text.course_title')</th>
                                <th>@lang('text.credit_value')</th>
                                <th>@lang('text.word_status')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-top border-bottom">
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                            </tr>
                            <tr class="border-top border-bottom">
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                            </tr>
                            <tr class="border-top border-bottom">
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                                <td class="border-left border-right">----</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    function loadSemesters(element){
        let bg = element.value;
        let url = "{{route('semesters', '__BG')}}";
        url = url.replace('__BG', bg);
        $.ajax({
            method:'get',
            url: url,
            success: function(data){
                console.log(data);
                let semester_ = `<select name="semester" class="form-control" required>`;
                data.forEach(element => {
                    semester_ = semester_+`<option value="`+element.id+`">`+element.name+`</option>`;
                });
                semester_ = semester_+`</select>`;
                $('#semesters').html(semester_);
            }
        })
    }
</script>
@endsection
