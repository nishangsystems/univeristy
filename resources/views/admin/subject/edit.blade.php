@extends('admin.layout')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="mx-3">
        <div class="form-panel">
            <form class="cmxform form-horizontal style-form text-capitalize" method="post" action="{{route('admin.subjects.update', $subject->id)}}">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="put" id="">
                <input type="hidden" name="semester" id="" value="{{$subject->semester_id}}}">
                <div class="text-center fw-bolder fs-2 text-secondary my-3 text-uppercase">{{\App\Models\Semester::find($subject->semester_id)->name ?? null}} {{__('text.word_course')}}</div>
                <div class="row my-2">
                    <label for="" class="col-md-3">{{__('text.word_title')}}</label>
                    <div class="col-md-9 col-lg-9">
                        <input type="text" name="name" id="" required value="{{$subject->name}}" class="form-control">
                    </div>
                </div>
                <div class="row my-2">
                    <label for="" class="col-md-3">{{__('text.course_code')}}</label>
                    <div class="col-md-9 col-lg-9">
                        <input type="text" name="code" id="" required value="{{$subject->code}}" class="form-control ">
                    </div>
                </div>
                <div class="row my-2">
                    <label for="" class="col-md-3">{{__('text.credit_value')}}</label>
                    <div class="col-md-9 col-lg-9">
                        <input type="number" name="coef" id="" min="1" required value="{{$subject->coef}}" class="form-control">
                    </div>
                </div>
                <div class="row my-2">
                    <label for="" class="col-md-3">{{__('text.word_level')}}</label>
                    <div class="col-md-9 col-lg-9">
                        <select name="level" id="" required class="form-control">
                            <option value="">{{__('text.select_level')}}</option>
                            @foreach(\App\Models\Level::all() as $level)
                            <option value="{{$level->id}}" {{$level->id == $subject->level_id ? 'selected' : ''}}>{{$level->level}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row my-2">
                    <label for="" class="col-md-3">{{__('text.word_status')}}</label>
                    <div class="col-md-9 col-lg-9">
                        <select name="status" id="" required class="form-control text-uppercase">
                            <option value="">{{__('text.select_status')}}</option>
                            <option value="C" {{$subject->status=='C' ? 'selected' : ''}}>C</option>
                            <option value="R" {{$subject->status=='R' ? 'selected' : ''}}>R</option>
                            <option value="G" {{$subject->status=='G' ? 'selected' : ''}}>G</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end my-3  text-capitalize">
                    <button class="btn btn-xs btn-primary" type="submit">{{__('text.word_save')}}</button> |
                    <a class="btn btn-xs btn-danger" href="{{route('admin.subjects.index')}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </form>
        </div>
        <hr>
        <div>
            <table class="table">
                <thead>
                    <th>###</th>
                    <th>{{__('text.word_title')}}</th>
                    <th>{{__('text.course_code')}}</th>
                    <th>{{__('text.credit_value')}}</th>
                    <th>{{__('text.word_level')}}</th>
                    <th>{{__('text.word_status')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\Subjects::where('semester_id', $subject->semester_id)->orderBy('updated_at', 'DESC')->get() as $subj)
                    <tr>
                        <td>{{$k++}}</td>
                        <td>{{$subj->name}}</td>
                        <td>{{$subj->code}}</td>
                        <td>{{$subj->coef}}</td>
                        <td>{{\App\Models\Level::find($subj->level_id)->level}}</td>
                        <td>{{$subj->status}}</td>
                        <td>
                            <a href="{{route('admin.subjects.edit', $subj->id)}}" class="btn btn-primary btn-sm">{{__('text.word_edit')}}</a> | 
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
