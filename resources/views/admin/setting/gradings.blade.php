@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="container-fluid">
            <table class="table">
                <thead class="text-capitalize ">
                    <th>@lang('text.sn')</th>
                    <th>@lang('text.grading_type')</th>
                    <th>@lang('text.word_grading')</th>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($gradings as $grading)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$grading->name}}</td>
                            <td>
                                <div class="accordion" id="accordion{{$grading->id}}">
                                    <div class="card">
                                        <div class="card-header" id="heading{{$grading->id}}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left text-uppercase" type="button" data-toggle="collapse" data-target="#collapse{{$grading->id}}" aria-expanded="true" aria-controls="collapse{{$grading->id}}">
                                                    <b>@lang('text.word_grading')</b>
                                                </button>
                                            </h2>
                                        </div>
                                        
                                        <div id="collapse{{$grading->id}}" class="collapse show" aria-labelledby="heading{{$grading->id}}" data-parent="#accordion{{$grading->id}}">
                                            <div class="card-body">
                                                <table>
                                                    <tbody>
                                                        <thead class="text-uppercase text-success">
                                                            <th>@lang('text.word_grade')</th>
                                                            <th>@lang('text.lower_limit')</th>
                                                            <th>@lang('text.upper_limit')</th>
                                                            <th>@lang('text.word_weight')</th>
                                                            <th>@lang('text.word_remark')</th>
                                                        </thead>
                                                        @foreach ($grading->grading as $grade)
                                                            <tr>
                                                                <th>{{$grade->grade}}</th>
                                                                <td>{{$grade->lower}}</td>
                                                                <td>{{$grade->upper}}</td>
                                                                <td>{{$grade->weight}}</td>
                                                                <td>{{$grade->remark}}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection