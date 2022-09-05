@extends('admin.layout')
@section('section')
 
        <div>
            <img width="100%" src="{{asset('assets/images')}}/header.jpg" />
        </div>
        <div style=" float:left; width:100%;TEXT-ALIGN:CENTER;  height:34px;font-size:24px;margin-bottom:20px; " class="text-uppercase">
            {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$boarding_fee->id}}
        </div>
        <div style=" float:left; width:900px; margin-top:0px; TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
            <div style=" float:left; width:200px; height:25px;font-size:17px;"> {{__('text.word_name')}} :</div>
            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:300px;margin-top:3px;">
                    {{$student->name}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                </div>
            </div>
            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:500px;margin-top:3px; text-transform:capitalize">
                   {{__('text.dormitort_fee')}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
            </div>

            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.academic_year')}}:</div>
            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:300px;margin-top:3px;">
                    {{$year}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
            </div>
            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                <div style=" float:left; width:700px; height:25px;font-size:17px;">
                    <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                        XAF {{number_format($boarding_fee->amount_payable)}}
                    </div>
                    <div style=" float:left; width:200px;margin-top:3px; text-transform:uppercase">
                        {{__('text.word_date')}}
                    </div>
                    <div style=" float:left; border-bottom:1px solid #000;margin-top:3px;">
                        {{$boarding_fee->updated_at->format('d/m/Y')}}
                    </div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px;"> <i class="text-capitalize">{{__('text.amount_in_words')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($boarding_fee->amount_payable)}}</i></div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px;"> <i class="text-capitalize">{{__('text.total_amount')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{__('text.currency_cfa')}} {{number_format($boarding_fee->total_amount)}}</i></div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px;"> <i class="text-capitalize">{{__('text.total_amount_in_words')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($boarding_fee->total_amount)}}</i></div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px;"> <i class="text-capitalize">{{__('text.balance_due')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{__('text.currency_cfa')}} {{number_format($boarding_fee->balance)}}</i></div>
                </div>
                @if ($boarding_fee->status == 0)
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px;"> <i class="text-capitalize">{{__('text.word_status')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i class="text-capitalize">{{__('text.word_incomplete')}}</i></div>
                </div>
                @endif
                @if ($boarding_fee->status == 1)
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px;"> <i class="text-capitalize">{{__('text.word_status')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i class="text-capitalize">{{__('text.word_completed')}}</i></div>
                </div>
                @endif
                <div style=" clear:both; height:10px"></div>

                <div style="float:left; margin:10px 50px; height:10px; text-transform:capitalize">
                    ___________________<br /><br />{{__('text.burser_signature')}}
                </div>

                <div style="float:right; margin:10px 10px; height:10px; text-transform:capitalize">
                    ___________________<br /><br />{{__('text.student_signature')}}
                </div>
            </div>
        </div>

        <!-- ---------------------------- -->


        <div>
            
            <img width="100%" src="{{asset('assets/images')}}/header.jpg" style="margin-top:80px;" />
        </div>
        <div style=" float:left; width:100%;TEXT-ALIGN:CENTER;  height:34px;font-size:24px;margin-bottom:20px; text-transform:uppercase">
            {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$boarding_fee->id}}
        </div>
        <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:300px;margin-top:3px;">
                    {{$student->name}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                </div>
            </div>
            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:500px;margin-top:3px; text-transform:capitalize">
                   {{__('text.dormitort_fee')}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
            </div>

            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.academic_year')}}:</div>
            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:300px;margin-top:3px;">
                    {{$year}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
            </div>
            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                <div style=" float:left; width:700px; height:25px;font-size:17px;">
                    <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                        {{__('text.currency_cfa')}} {{number_format($boarding_fee->amount_payable)}}
                    </div>
                    <div style=" float:left; width:200px;margin-top:3px;  text-transform:uppercase">
                        {{__('text.word_date')}}
                    </div>
                    <div style=" float:left; border-bottom:1px solid #000;margin-top:3px;">
                        {{$boarding_fee->updated_at->format('d/m/Y')}}
                    </div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($boarding_fee->amount_payable)}}</i></div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.total_amount')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{__(''text.currency_cfa)}} {{number_format($boarding_fee->total_amount)}}</i></div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.total_amount_in_words')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($boarding_fee->total_amount)}}</i></div>
                </div>
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{__('text.currency_cfa')}} {{number_format($boarding_fee->balance)}}</i></div>
                </div>
                @if ($boarding_fee->status == 0)
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.word_status')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000;  text-transform:capitalize"><i>{{__('text.word_incomplete')}}</i></div>
                </div>
                @endif
                @if ($boarding_fee->status == 1)
                <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.word_status')}}</i></div>
                    <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000;  text-transform:capitalize"><i>{{__('text.word_completed')}}</i></div>
                </div>
                @endif
                <div style=" clear:both; height:10px"></div>

                <div style="float:left; margin:10px 50px; height:10px; text-transform:capitalize ">
                    ___________________<br /><br />{{__('text.burser_signature')}}
                </div>

                <div style="float:right; margin:10px 10px; height:10px;">
                    ___________________<br /><br />{{__('text.student_signature')}}
                </div>
            </div>
        </div>
            
@endsection
@section('script')
<script>
    window.print()
   
    
</script>
@endsection
