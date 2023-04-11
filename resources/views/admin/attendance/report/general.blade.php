@extends('admin.layout')
@section('section')
@php
    use Illuminate\Support\Facades\Date;
@endphp
    <div class="py-4">
        <table class="table table-light">
            <thead class="text-capitalize">
                <th>{{__('text.sn')}}</th>
                <th>{{__('text.word_name')}}</th>
                <th>{{__('text.no_of_courses')}}</th>
                <!-- <th>{{__('text.word_courses')}}</th> -->
                <th>{{__('text.hours_covered')}}</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach ($report as $group)
                    @php(
                        $hours = array_sum(array_map(function($el)use($group){
                                    $rec = $group->where('id', $el)->first();
                                    //dd($rec->toArray());
                                    return Date::parse($rec->check_in)->floatDiffInHours($rec->check_out);
                                }, 
                                $group->pluck('id')->toArray())))
                    <tr>
                        <td class="border-left border-right">{{$k++}}</td>
                        <td class="border-left border-right">{{$group->first()->name??null}}</td>
                        <td class="border-left border-right">{{$group->groupBy('subject_id')->count()}}</td>
                        <td class="border-left border-right">{{round($hours)}}</td>
                        <td class="border-left border-right">
                            <button class="btn btn-sm btn-primary" onclick="print('report_{{$group->first()->teacher_id}}')">{{__('text.word_print')}}</button>
                            <div style="width: 100vw; padding: 2rem 5rem; background-color: white" class="hidden" id="report_{{$group->first()->teacher_id}}">
                                <div class="w-100">
                                    <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" style="width: 100%; height: auto;">
                                    <h4 class="text-capitalize text-center"><b>{{__('text.cash_reciept')}} N<SUP>0</SUP> {{$group->first()->teacher_id}}</b></h4>
                                    <hr>
                                    <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" text-align:center; width:300px;margin-top:3px;">
                                                {{$group->first()->name??null}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:500px;margin-top:3px;">
                                                {{$title}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        </div>

                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.academic_year')}}:</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:300px;margin-top:3px;">
                                                {{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                            <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                                <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                    {{__('text.currency_cfa')}} {{round($hours)}}
                                                </div>
                                                <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                    {{__('text.word_date')}}
                                                </div>
                                                <div style=" float:left; border-bottom:1px solid #000;">
                                                    {{now()->format('d/m/Y')}}
                                                </div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord(round($hours))}}</i></div>
                                            </div>
                                            
                                            
                                            <div style=" clear:both; height:30px"></div>

                                            <div style="float:left; margin:30px 30px; height:30px; text-transform:capitalize">
                                                ___________________<br /><br />{{__('text.burser_signature')}}
                                            </div>

                                            <div style="float:right; margin:10px 10px; height:30px; text-transform:capitalize">
                                                ___________________<br /><br />{{__('text.student_signature')}}
                                            </div>
                                            
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
        function print(params) {
            let printable = document.querySelector('#'+params).innerHTML;
            let doc = document.body.innerHTML;
            document.body.innerHTML = printable;
            window.print();
            document.innerHTML = doc;

        }
    </script>
@endsection