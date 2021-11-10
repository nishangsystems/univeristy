@extends('admin.layout')
@section('section')
        <div>
            <img width="100%" src="{{asset('assets/images')}}/header.jpg" />
        </div>
        <div style=" float:left; width:100%;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; ">
            CASH RECEIPT N<SUP>0</SUP> 00{{$boarding_fee->id}}
        </div>
        <div style=" float:left; width:720px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
            <div style=" float:left; width:170px; height:25px;font-size:17px;"> Name :</div>
            <div style=" float:left; width:500px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:300px;margin-top:3px;">
                    {{$student->name}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:3px;">

                </div>
            </div>
            <div style=" float:left; width:170px; height:25px;font-size:17px;"> Purpose :</div>
            <div style=" float:left; width:500px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:500px;margin-top:3px;">
                   Dormitory Fee
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:3px;"></div>
            </div>

            <div style=" float:left; width:170px; height:25px;font-size:17px;"> Academic year:</div>
            <div style=" float:left; width:500px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                <div style=" float:left; width:300px;margin-top:3px;">
                    {{$year}}
                </div>
                <div style=" float:left; width:200px;  height:25px;margin-top:3px;"></div>
            </div>
            <div style=" float:left; width:700px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                <div style=" float:left; width:170px; height:25px;font-size:17px;"> Amount in Figure</div>
                <div style=" float:left; width:500px; height:25px;font-size:17px;">
                    <div style=" float:left; width:200px;border:1px solid #000;margin-top:3px;">
                        XAF {{number_format($boarding_fee->amount_payable)}}
                    </div>
                    <div style=" float:left; width:100px;margin-top:3px;">
                        DATE
                    </div>
                    <div style=" float:left; border-bottom:1px solid #000;margin-top:3px;">
                        {{$boarding_fee->created_at->format('d/m/Y')}}
                    </div>
                </div>
                <div style=" float:left; width:700px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:170px; height:25px;font-size:17px;"> <i>Amount in Words</i></div>
                    <div style=" float:left; width:500px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($boarding_fee->amount_payable)}}</i></div>
                </div>
                <div style=" float:left; width:700px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:180px; height:25px;font-size:17px;"> <i>Total Amount</i></div>
                    <div style=" float:left; width:500px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>XAF {{number_format($boarding_fee->total_amount)}}</i></div>
                </div>
                <div style=" float:left; width:700px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:195px; height:25px;font-size:17px;"> <i>Total Amount in Words</i></div>
                    <div style=" float:left; width:500px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($boarding_fee->total_amount)}}</i></div>
                </div>
                <div style=" float:left; width:700px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:170px; height:25px;font-size:17px;"> <i>Balance Due</i></div>
                    <div style=" float:left; width:500px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>XAF {{number_format($boarding_fee->balance)}}</i></div>
                </div>
                @if ($boarding_fee->status == 0)
                <div style=" float:left; width:700px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:170px; height:25px;font-size:17px;"> <i>Status</i></div>
                    <div style=" float:left; width:500px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>Incomplete</i></div>
                </div>
                @endif
                @if ($boarding_fee->status == 1)
                <div style=" float:left; width:700px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                    <div style=" float:left; width:170px; height:25px;font-size:17px;"> <i>Status</i></div>
                    <div style=" float:left; width:500px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>Complete</i></div>
                </div>
                @endif
                <div style=" clear:both; height:10px"></div>

                <div style="float:left; margin:10px 10px; height:10px; ">
                    ___________________<br /><br />Bursar Signature
                </div>

                <div style="float:right; margin:10px 10px; height:10px;">
                    ___________________<br /><br />Student Signature
                </div>

            </div>
        </div>
@endsection
@section('script')
<script>

    window.print()


</script>
@endsection
