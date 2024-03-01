@extends('admin.layout')
@section('section')
    <div class="py-3 px-4 border" id="clearance_panel">
        <div class="text-center h3 font-weight-bold my-4 text-underline">@lang('text.clearance_univ_name')</div>
        <div class="d-flex justify-content-center py-2">
            <img src="{{ asset('assets/images/avatars/logo.png') }}" alt="" srcset="" style="width: 14rem; height: auto;">
        </div>
        <div class="py-2">
            <div class="font-weight-bold h6">@lang('text.department_of_finance')</div>
            <small class="text-capitalize">@lang('text.our_reference') : <span class="font-weight-semibold text-underline">21/24/2931</span></small>
        </div>
        <div class="text-center h4 font-weight-bold my-4 text-uppercase text-underline">@lang('text.to_whom_it_may_concern')</div>
        <div class="h6 font-weight-bold my-2 text-uppercase text-underline">@lang('text.fees_clearance')</div>
        <div>{!! __('text.clearance_text', ['name'=>'STUDENT NAME', 'matric'=>'XYS907', 'program'=>'PROGRAM NAME', 'school'=>'SCHOOL', 'adm_year'=>'ADMISSION BATCH', 'fin_year'=>'FINAL YEAR']) !!}</div>
        <table class="my-5 border">
            <thead class="text-uppercase border-top border-bottom">
                <th class="border-left border-right">@lang('text.word_year')/@lang('text.word_level')</th>
                <th class="border-left border-right">@lang('text.tution_fees_paid')</th>
                <th class="border-left border-right">@lang('text.word_scholarship')</th>
                <th class="border-left border-right">@lang('text.registration_fees_paid')</th>
            </thead>
            <tbody>
                @foreach([1,2,3,4,5] as $key => $value)
                    <tr class="border-top border-bottom">
                        <td class="border-left border-right">{{ $value }}</td>
                        <td class="border-left border-right"></td>
                        <td class="border-left border-right"></td>
                        <td class="border-left border-right"></td>
                    </tr>
                @endforeach
                <tr class="border-top border-bottom">
                    <th class="border-left border-right text-uppercase">@lang('text.word_total')</th>
                    <th class="border-left border-right"></th>
                    <th class="border-left border-right"></th>
                    <th class="border-left border-right"></th>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
@section('script')
    
@endsection