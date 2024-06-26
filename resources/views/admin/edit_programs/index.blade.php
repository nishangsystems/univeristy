@extends('admin.layout')
@section('section')
    <div class="py-2">
        <table class="table">
            <thead class="text-capitalize">
                <th>@lang('text.sn')</th>
                <th>@lang('text.word_program')</th>
                <th>@lang('text.grading_type')</th>
                <th></th>
            </thead>
            <tbody>
                @php
                    $c = 1;
                @endphp
                @foreach ($programs as $program)
                    <tr class="border-bottom">
                        <td class="border-left">{{ $c++ }}</td>
                        <td class="border-left">{{ $program->name??'' }}</td>
                        <td class="border-left">{{  $program->gradingType->name??'' }}</td>
                        <td class="border-left">
                            <a class="btn btn-sm btn-primary rounded" href="{{ route('admin.units.edit', $program->id) }}?pms=1">@lang('text.word_edit')</a>
                            <a class="btn btn-sm btn-secondary rounded" data-toggle="modal" href="#__{{ $program->id }}gradingSystemModal">@lang('text.word_grading')</a>
                            {{-- grading system details modal --}}
                            <div id="__{{ $program->id }}gradingSystemModal" class="modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div id="modal-wizard-container">
                                            <div class="modal-header">
                                                <h4 class="heading text-capitalize">{{ $program->gradingType->name??'' }}</h4>
                                            </div>

                                            <div class="modal-body step-content">
                                                <div class="container-fluid">
                                                    <table class="table-stripped table-light shadow">
                                                        <thead class="text-capitalize">
                                                            <th>@lang('text.sn')</th>
                                                            <th>@lang('text.word_grade')</th>
                                                            <th>@lang('text.lower_limit')</th>
                                                            <th>@lang('text.upper_limit')</th>
                                                            <th>@lang('text.word_weight')</th>
                                                            <th>@lang('text.word_remark')</th>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $k = 1;
                                                            @endphp
                                                            @foreach (optional($program->gradingType)->grading??[] as $grading)
                                                                <tr class="border-bottom">
                                                                    <td class="border-left border-right">{{ $k++; }}</td>
                                                                    <td class="border-left border-right">{{ $grading->grade }}</td>
                                                                    <td class="border-left border-right">{{ $grading->lower }}</td>
                                                                    <td class="border-left border-right">{{ $grading->upper }}</td>
                                                                    <td class="border-left border-right">{{ $grading->weight }}</td>
                                                                    <td class="border-left border-right">{{ $grading->remark }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer wizard-actions">
                                            <button class="btn btn-danger btn-sm pull-left text-capitalize" data-dismiss="modal">
                                                <i class="ace-icon fa fa-times"></i>
                                                @lang('text.word_close')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- PAGE CONTENT ENDS -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection