@extends('admin.layout')
@section('section')
<?php
    use Illuminate\Support\Facades\Date;
?>
    <div class="py-4">
        <table class="table table-light">
            <thead class="text-capitalize">
                <th>{{__('text.sn')}}</th>
                <th>{{__('text.word_name')}}</th>
                <th>{{__('text.no_of_courses')}}</th>
                <th>{{__('text.hours_covered')}}</th>
                <th>{{__('text.word_rate')}}</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach ($report as $group)
                    <?php

                        $hours = array_sum(array_map(function($el)use($group){
                                    $rec = $group->where('id', $el)->first();
                                    //dd($rec->toArray());
                                    return Date::parse($rec->check_in)->floatDiffInHours($rec->check_out);
                                }, 
                                $group->pluck('id')->toArray()));
                    
                        $sbjt_groups = $group->groupBy('subject_id');
                        $amt = 0;
                        foreach ($sbjt_groups as $key => $sb_g) {
                            # code...
                            // dd($sb_g);
                            $hrs = array_sum(array_map(function($el)use($sb_g){
                                $_rec = $sb_g->where('id', $el)->first();
                                //dd($rec->toArray());
                                return Date::parse($_rec->check_in)->floatDiffInHours($_rec->check_out);
                            }, 
                            $sb_g->pluck('id')->toArray()));
                            $amt += \App\Http\Controllers\Controller::get_payment_rate($sb_g->first()->teacher_id, $sb_g->first()->subject->level_id)*round($hrs);
                            // dd($hrs);
                        }
                        // $amount = array_sum(array_map(function($el)use($group){
                        //             $rec = $group->where('id', $el)->first();
                        //             //dd($rec->toArray());
                        //             return round(Date::parse($rec->check_in)->floatDiffInHours($rec->check_out));
                        //         }, 
                        //         $group->pluck('id')->toArray()));
                    ?>
                    <tr>
                        <td class="border-left border-right">{{$k++}}</td>
                        <td class="border-left border-right">{{$group->first()->name??null}}</td>
                        <td class="border-left border-right">{{$group->groupBy('subject_id')->count()}}</td>
                        <td class="border-left border-right">{{round($hours)}}</td>
                        <td class="border-left border-right">{{number_format($amt)}}</td>
                        <td class="border-left border-right">
                            <button class="btn btn-sm btn-primary" onclick="__print('report_{{$group->first()->teacher_id}}')">{{__('text.word_print')}}</button>
                            <div style="width: 100vw; padding: 2rem 5rem; background-color: white;" class="hidden" id="report_{{$group->first()->teacher_id}}">
                                <div class="w-100 bg-white">
                                    <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" style="width: 100%; height: auto;">
                                    <hr>
                                    <div class="" style="background-color: white;">
                                        <div class="row py-3 border-top border-bottom">
                                            <div class="col-md-6">
                                                <div class="row py-3">
                                                    <div class="col-sm-4 text-left text-capitalize h4 text-info">{{__('text.word_name')}}</div>
                                                    <div class="col-sm-8 text-left text-capitalize h4 text-primary">:   {{$group->first()->name??null}}</div>
                                                </div>
                                                <div class="row py-3">
                                                    <div class="col-sm-4 text-left text-capitalize h4 text-info">{{__('text.word_matricule')}}</div>
                                                    <div class="col-sm-8 text-left text-capitalize h4 text-primary">:   {{$group->first()->matric??null}}</div>
                                                </div>
                                                <div class="row py-3">
                                                    <div class="col-sm-4 text-left text-capitalize h4 text-info">{{__('text.word_purpose')}}</div>
                                                    <div class="col-sm-8 text-left text-capitalize h4 text-primary">:   {{__('text.salary_slip_for').' '.date('F Y', strtotime(request('month')))}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="py-3">
                                            <table>
                                                <thead class="text-capitalize bg-light">
                                                    <th class="border">{{__('text.sn')}}</th>
                                                    <th class="border">{{__('text.course_title')}}</th>
                                                    <th class="border">{{__('text.course_code')}}</th>
                                                    <th class="border">{{__('text.word_rate')}}</th>
                                                    <th class="border">{{__('text.word_hours')}}</th>
                                                    <th class="border">{{__('text.word_amount')}}</th>
                                                </thead>
                                                <tbody>
                                                    @php($k = 1)
                                                    @php($rate_hrs = [])
                                                    @foreach ($group->groupBy('subject_id') as $course_record)
                                                        <?php 
                                                            $c_hrs = round(array_sum(array_map(function($el)use($course_record){
                                                                $_rec = $course_record->where('id', $el)->first();
                                                                //dd($rec->toArray());
                                                                return Date::parse($_rec->check_in)->floatDiffInHours($_rec->check_out);
                                                            }, 
                                                            $course_record->pluck('id')->toArray())));
                                                            $rate = \App\Http\Controllers\Controller::get_payment_rate($course_record->first()->teacher_id, $course_record->first()->subject->level_id);
                                                            $rate_hrs[] = $rate * $c_hrs;
                                                         ?>
                                                        <tr>
                                                            <td class="border">{{$k++}}</td>
                                                            <td class="border">{{$course_record->first()->subject->name??null}}</td>
                                                            <td class="border">{{$course_record->first()->subject->code??null}}</td>
                                                            <td class="border">{{number_format($rate)}}</td>
                                                            <td class="border">{{$c_hrs}}</td>
                                                            <td class="border">{{number_format($c_hrs*$rate)}}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="5" class="text-left border text-capitalize">{{__('text.word_total')}}</td>
                                                        <td class="border">{{number_format(array_sum($rate_hrs))}}</td>
                                                    </tr>
                                                    <tr class="pt-5 my-5 text-capitalize">
                                                        <td colspan="2" class="text-left h4">{{__('text.amount_in_words')}}</td>
                                                        <td colspan="4" class="text-left h4 border-bottom">{{\App\Helpers\Helpers::instance()->numToWord(array_sum($rate_hrs))}}</td>
                                                    </tr>
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
@endsection
@section('script')
    <script>
        function __print(params) {
            let printable = document.querySelector('#'+params).innerHTML;
            let doc = document.body.innerHTML;
            document.body.innerHTML = printable;
            window.print();
            document.body.innerHTML = doc;
        }
    </script>
@endsection