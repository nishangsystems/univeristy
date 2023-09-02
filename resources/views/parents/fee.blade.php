@extends('parents.layout')
@section('section')
    @php
        $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $header = \App\Helpers\Helpers::instance()->getHeader();
    @endphp

    <div class="col-sm-12">
        @if($student->bal($student->id, $year) > 0)
            <div class="d-flex justify-content-end text-uppercase my-3"><a class="btn btn-sm btn-success text-uppercase" href="{{ route('parents.tranzak.pay_fee', $student->id) }}">{{ __('text.pay_fee') }}</a></div>
        @endif
        <div class="d-flex flex-wrap justify-content-between alert alert-info text-center text-uppercase my-3"><span>{{__('text.total_paid').' : '.number_format($student->total_paid( $year)).' '.__('text.currency_cfa')}}</span><span>{{__('text.total_debts').' : '.number_format($student->bal($student->id, $year)).' '.__('text.currency_cfa')}}</span></div>
        <div class="content-panel">
            <form method="get">
                <div class="input-group input-group-merge border">
                    <select name="year" id="" class="form-control border-0" required>
                        <option value="">{{__('text.academic_year')}}</option>
                        @foreach(\App\Models\Batch::all() as $batch)
                            <option value="{{$batch->id}}">{{$batch->name}}</option>
                        @endforeach
                    </select>
                    <input type="submit" class="btn btn-sm btn-light border-0 rounded-0" value="{{__('text.word_get')}}">
                </div>
            </form>
        </div>
        <div class="content-panel">
            @forelse($student->payments()->where(['batch_id'=>(request('year') ?? \App\Helpers\Helpers::instance()->getYear())])->get() as $item)
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <div>
                        <div>{{($item->item) ? $item->item->name : $item->created_at}}</div>
                        <h4 class="font-weight-bold">{{number_format($item->amount)}} FCFA</h4>
                        <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->created_at))}}</span>
                        <br>
                        <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.$item->reference_number}}</span>
                    </div>
                    <btn class="btn btn-sm btn-primary" onclick="printDiv('printHERE{{$item->id}}')">{{__('text.word_print')}}</btn>
                    <!-- create a hidden div for printable markup and print with js on request -->
                    <div class="d-none">
                        <div id="printHERE{{$item->id}}" class="eachrec">
                            
                           <div class="mb-5">
                            <div style="height:120px; width:95% ; ">
                                    <img width="100%" src="{{$header}}" />
                                </div>
                                <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform: uppercase;">
                                    {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}}
                                </div>
                                <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" text-align:center; width:300px;margin-top:3px;">
                                            {{$student->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                                        </div>
                                    </div>
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:500px;margin-top:3px;">
                                            {{($item->item) ? $item->item->name : $student->_class($year)->name().' - Fees '}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    </div>

                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.academic_year')}}:</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:300px;margin-top:3px;">
                                            {{\App\Models\Batch::find($year)->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                    </div>
                                    <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$item->amount}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000;">
                                                {{$item->created_at->format('d/m/Y')}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->amount)}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.extra_fee')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$student->extraFee($year) ? $student->extraFee($year)->amount : ''}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000;">
                                                {{date('d/m/Y', strtotime($student->extraFee($year) ? $student->extraFee($year)->created_at : ''))}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($student->extraFee($year) ? $student->extraFee($year)->amount : 0)}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{$student->bal($student->id)}}</i></div>
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
                           <div>
                                <div style="height:120px; width:95% ; margin-top:550px;">
                                    <img width="100%" src="{{$header}}" />
                                </div>
                                <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                    {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}}
                                </div>
                                <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" text-align:center; width:300px;margin-top:3px;">
                                            {{$student->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                                        </div>
                                    </div>
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:500px;margin-top:3px;">
                                            {{($item->item) ? $item->item->name : $student->_class($year)->name().' - Fees '}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    </div>

                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.academic_year')}}:</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:300px;margin-top:3px;">
                                            {{\App\Models\Batch::find($year)->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                    </div>
                                    <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$item->amount}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000; margin-top:53px;">
                                                {{$item->created_at->format('d/m/Y')}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->amount)}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.extra_fee')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$student->extraFee($year) ? $student->extraFee($year)->amount : ''}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000;">
                                                {{date('d/m/Y', strtotime($student->extraFee($year) ? $student->extraFee($year)->created_at : ''))}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($student->extraFee($year) ? $student->extraFee($year)->amount : 0)}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{$student->bal($student->id)}}</i></div>
                                        </div>
                                        
                                        <div style=" clear:both; height:30px"></div>

                                        <div style="float:left; margin:30px 30px; height:30px; text-transform:capitalize;">
                                            ___________________<br /><br />{{__('text.burser_signature')}}
                                        </div>

                                        <div style="float:right; margin:10px 10px; height:30px; text-transform:capitalize">
                                            ___________________<br /><br />{{__('text.student_signature')}}
                                        </div>
                                    </div>
                                </div>
                           </div>
                            
                        </div>
                    </div>
                    <!-- ------------------------------- -->


                </div>
                @if($item->debt != 0 && $item->debt != null)
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <div>
                            <div>{{__('text.word_debt')}}</div>
                            <h4 class="font-weight-bold">{{number_format($item->debt)}} FCFA</h4>
                            <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->created_at))}}</span>
                            <br>
                            <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.$item->reference_number}}</span>
                        </div>
                        <btn class="btn btn-sm btn-primary" onclick="printDiv('_printHERE{{$item->id}}')">{{__('text.word_print')}}</btn>
                        <!-- create a hidden div for printable markup and print with js on request -->
                        <div class="d-none">
                            <div id="_printHERE{{$item->id}}" class="eachrec">
                                
                               <div class="mb-5">
                               <div style="height:120px; width:95% ; ">
                                    <img width="100%" src="{{$header}}" />
                                </div>
                                <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform: uppercase;">
                                    {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}}
                                </div>
                                <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" text-align:center; width:300px;margin-top:3px;">
                                            {{$student->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                                        </div>
                                    </div>
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:500px;margin-top:3px; text-transform: uppercase;">
                                            {{($item->item) ? $item->item->name.' '.__('text.word_debt') : $student->_class($year)->name().' - Debt '}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    </div>

                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.academic_year')}}:</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:300px;margin-top:3px;">
                                            {{\App\Models\Batch::find($year)->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                    </div>
                                    <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$item->debt}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000;">
                                                {{$item->created_at->format('d/m/Y')}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->debt)}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.extra_fee')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$student->extraFee($year) ? $student->extraFee($year)->amount : ''}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000;">
                                                {{date('d/m/Y', strtotime($student->extraFee($year) ? $student->extraFee($year)->created_at : ''))}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($student->extraFee($year) ? $student->extraFee($year)->amount : 0)}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{$student->bal($student->id)}}</i></div>
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
                               <div>
                                  
                                    <div style="height:120px; width:95% ; margin-top:550px;">
                                        <img width="100%" src="{{$header}}" />
                                    </div>
                                    <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                    {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}}
                                </div>
                                <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" text-align:center; width:300px;margin-top:3px;">
                                            {{$student->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                                        </div>
                                    </div>
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:500px;margin-top:3px; text-transform: uppercase;">
                                            {{($item->item) ? $item->item->name.' '.__('text.word_debt') : $student->_class($year)->name().' - Debt '}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    </div>

                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.academic_year')}}:</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:300px;margin-top:3px;">
                                            {{\App\Models\Batch::find($year)->name}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                    </div>
                                    <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$item->debt}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000; margin-top:53px;">
                                                {{$item->created_at->format('d/m/Y')}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->debt)}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.extra_fee')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$student->extraFee($year) ? $student->extraFee($year)->amount : ''}}
                                            </div>
                                            <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                {{__('text.word_date')}}
                                            </div>
                                            <div style=" float:left; border-bottom:1px solid #000;">
                                                {{date('d/m/Y', strtotime($student->extraFee($year) ? $student->extraFee($year)->created_at : ''))}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($student->extraFee($year) ? $student->extraFee($year)->amount : '')}}</i></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{$student->bal($student->id)}}</i></div>
                                        </div>
                                        
                                        <div style=" clear:both; height:30px"></div>

                                        <div style="float:left; margin:30px 30px; height:30px; text-transform:capitalize;">
                                            ___________________<br /><br />{{__('text.burser_signature')}}
                                        </div>

                                        <div style="float:right; margin:10px 10px; height:30px; text-transform:capitalize">
                                            ___________________<br /><br />{{__('text.student_signature')}}
                                        </div>
                                    </div>
                                </div>
                               </div>
                                
                            </div>
                        </div>
                        <!-- ------------------------------- -->


                    </div>
                @endif
            @empty
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <p>No Fee Collection where found, for <b>{{\App\Models\Batch::find($year)->name}}</b> </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
@section('script')
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection