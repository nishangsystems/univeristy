@extends('student.layout')
@section('section')
    @php
        $student = auth('student')->user();
        $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $header = \App\Helpers\Helpers::instance()->getHeader();
    @endphp

    <div class="col-sm-12">
        <div class="bg-secondary">
            <div class="bg-light py-2 border-top border-bottom border-dark text-uppercase text-center">
                {{ __('text.word_tution') }}
            </div>
            <div class="content-panel">
                @forelse($fees as $item)
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <div>
                            <div class="text-uppercase">{{__('text.word_tution').' : '.\App\Models\Batch::find($item->batch_id)->name}}</div>
                            <h4 class="font-weight-bold">{{number_format($item->amount)}} FCFA</h4>
                            <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->transaction->created_at ?? $item->created_at))}}</span>
                            <br>
                            <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.($item->transaction->financialTransactionId??($item->transaction->transaction_id ?? null))}}</span>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="printDiv('fee_printHERE{{$item->id}}')">{{__('text.word_print')}}</button>
                        <!-- create a hidden div for printable markup and print with js on request -->
                        <div class="d-none">
                            <div id="fee_printHERE{{$item->id}}" class="eachrec">
                                <div class="mb-5">
                                    <div style="height:120px; width:95% ;">
                                        <img width="100%" src="{{$header}}" />
                                    </div>
                                    <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                        {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}} <span style="font-size: medium; font-style: italic;">({{ __('text.word_reference') }} : {{ $item->transaction->financialTransactionId ?? $item->transaction->transaction_id ?? null }})</span>
                                    </div>
                                    <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" text-align:center; width:80%; margin-top:3px;">
                                                {{$student->name}}
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
                                        
                                        <div style=" float:left; width:200px;  height:25px;margin-top:5px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:200px; font-size:13px; ">
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
                                                    {{$student->extraFee($year) ? date('d/m/Y', strtotime( $student->extraFee($year)->created_at)) : ''}}
                                                </div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:15px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:18px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:15px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($student->extraFee($year) ? $student->extraFee($year)->amount : 0)}}</i></div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:15px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:18px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:15px; font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{$student->bal($student->id)}}</i></div>
                                            </div>
                                            
                                            <div style=" clear:both; height:20px"></div>

                                            <div style="float:left; margin:10px 10px; height:30px; text-transform:capitalize;">
                                                ___________________<br /><br />{{__('text.burser_signature')}}
                                            </div>

                                            <div style="float:right; margin:10px 10px; height:30px; text-transform:capitalize">
                                                ___________________<br /><br />{{__('text.student_signature')}}
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                                <div>
                                    <div style="height:120px; width:95% ; margin-top: 65rem;">
                                        <img width="100%" src="{{$header}}" />
                                    </div>
                                    <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                        {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}} <span style="font-size: medium; font-style: italic;">({{ __('text.word_reference') }} : {{ $item->transaction->financialTransactionId ?? $item->transaction->transaction_id ?? null }})</span>
                                    </div>
                                    <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" text-align:center; width:80%; margin-top:3px;">
                                                {{$student->name}}
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
                                        
                                        <div style=" float:left; width:200px;  height:25px;margin-top:5px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:200px; font-size:13px; ">
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
                                                    {{$student->extraFee($year) ? date('d/m/Y', strtotime( $student->extraFee($year)->created_at)) : ''}}
                                                </div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:15px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:18px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:15px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($student->extraFee($year) ? $student->extraFee($year)->amount : 0)}}</i></div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:15px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:18px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:15px; font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{$student->bal($student->id)}}</i></div>
                                            </div>
                                            
                                            <div style=" clear:both; height:20px"></div>

                                            <div style="float:left; margin:10px 10px; height:30px; text-transform:capitalize;">
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
                @empty
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <p>No Payments where found </p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="bg-secondary">
            <div class="bg-light py-2 border-top border-bottom border-dark text-uppercase text-center">
                {{ trans_choice('text.word_transcript', 2) }}
            </div>
            <div class="content-panel">
                @forelse($transcripts as $item)
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <div>
                            <div class="text-uppercase">{{$item->config->mode.($item->semester_id > 0 ? ' - '.\App\Models\Semester::find($item->semester_id)->name : '').' : '.\App\Models\Batch::find($item->year_id)->name}}</div>
                            <h4 class="font-weight-bold">{{number_format($item->transaction->amount ?? null)}} FCFA</h4>
                            <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->transaction->created_at ?? $item->created_at))}}</span>
                            <br>
                            <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.($item->transaction->financialTransactionId??($item->transaction->transaction_id ?? null))}}</span>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="printDiv('trans_printHERE{{$item->id}}')">{{__('text.word_print')}}</button>
                        <!-- create a hidden div for printable markup and print with js on request -->
                        <div class="d-none">
                            <div id="trans_printHERE{{$item->id}}" class="eachrec">
                                
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
                                        <div style=" text-align:center; width:80%; margin-top:3px;">
                                            {{$student->name}}
                                        </div>
                                    </div>
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:500px;margin-top:3px; text-transform: uppercase;">
                                            {{(($item->config) ? $item->config->mode : $student->_class($year)->name()).' - '.trans_choice('text.word_transcript', 1)}}
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
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.word_reference')}}:</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:80%; margin-top:3px;">
                                            {{$item->transaction->financialTransactionId??($item->transaction->transaction_id ?? null)}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                    </div>
                                    <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$item->transaction->amount}}
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
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->transaction->amount)}}</i></div>
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
                                <div style="height:120px; width:95% ; margin-top: 65rem;">
                                    <img width="100%" src="{{$header}}" />
                                </div>
                                <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                    {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}}
                                </div>
                                <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" text-align:center; width:80%; margin-top:3px;">
                                            {{$student->name}}
                                        </div>
                                    </div>
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:500px;margin-top:3px; text-transform: uppercase;">
                                            {{(($item->config) ? $item->config->mode : $student->_class($year)->name()).' - '.trans_choice('text.word_transcript', 1)}}
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
                                    <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.word_reference')}}:</div>
                                    <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                        <div style=" float:left; width:80%; margin-top:3px;">
                                            {{$item->transaction->financialTransactionId??($item->transaction->transaction_id ?? null)}}
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                    </div>
                                    <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                    <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                        <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                            <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                {{__('text.currency_cfa')}} {{$item->transaction->amount}}
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
                                            <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->transaction->amount)}}</i></div>
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
                        </div>
                        <!-- ------------------------------- -->
                    </div>
                @empty
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <p>No Payments where found </p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="bg-secondary">
            <div class="bg-light py-2 border-top border-bottom border-dark text-uppercase text-center">
                {{ __('text.other_payments') }}
            </div>
            <div class="content-panel">
                @forelse($other_payments as $item)
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <div>
                            <div class="text-uppercase">{{$item->income->name.' : '.$item->batch->name}}</div>
                            <h4 class="font-weight-bold">{{number_format($item->transaction->amount ?? $item->income->amount)}} FCFA</h4>
                            <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->transaction->created_at ?? $item->created_at))}}</span>
                            <br>
                            <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.($item->transaction->financialTrancationId ?? $item->transaction->transaction_id)}}</span>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="printDiv('other_printHERE{{$item->id}}')">{{__('text.word_print')}}</button>
                        <!-- create a hidden div for printable markup and print with js on request -->
                        <div class="d-none">
                            <div id="other_printHERE{{$item->id}}" class="eachrec">
                                
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
                                            <div style=" text-align:center; width:80%; margin-top:3px;">
                                                {{$student->name}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:500px;margin-top:3px;">
                                                {{($item->income) ? $item->income->name : ($item->transaction->payment_purpose ?? 'OTHER PAYMENTS')}}
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

                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.word_reference')}}:</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:300px;margin-top:3px;">
                                                {{$item->transaction->financialTrancationId ?? $item->transaction->transaction_id}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                            <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                                <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                    {{__('text.currency_cfa')}} {{$item->transaction->amount ?? $item->income->amount}}
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
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->transaction->amount ?? $item->income->amount)}}</i></div>
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
                                    <div style="height:120px; width:95% ; margin-top: 65rem;">
                                        <img width="100%" src="{{$header}}" />
                                    </div>
                                    <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                        {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}}
                                    </div>
                                    <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" text-align:center; width:80%; margin-top:3px;">
                                                {{$student->name}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:500px;margin-top:3px;">
                                                {{($item->income) ? $item->income->name : ($item->transaction->payment_purpose ?? 'OTHER PAYMENTS')}}
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
                                        
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.word_reference')}}:</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:300px;margin-top:3px;">
                                                {{$item->transaction->financialTrancationId ?? $item->transaction->transaction_id}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                            <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                                <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                    {{__('text.currency_cfa')}} {{$item->transaction->amount ?? $item->income->amount}}
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
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($item->transaction->amount ?? $item->income->amount)}}</i></div>
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
                        </div>
                        <!-- ------------------------------- -->


                    </div>
                @empty
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <p>No Payments where found </p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="bg-secondary">
            <div class="bg-light py-2 border-top border-bottom border-dark text-uppercase text-center">
                {{ __('text.word_charges') }}
            </div>
            <div class="content-panel">
                @forelse($charges as $item)
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <div>
                            <div class="text-uppercase">{{$item->type.' charges : '.$item->batch->name}}</div>
                            <h4 class="font-weight-bold">{{number_format($item->amount)}} FCFA</h4>
                            <span class="font-weight-bolder h5 text-uppercase">{{__('text.paid_on').' '.date('d/m/Y', strtotime($item->created_at))}}</span>
                            <br>
                            <span class="font-weight-bolder h6 text-capitalize text-secondary">{{__('text.word_ref').'. '.($item->financialTrancationId)}}</span>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="printDiv('printHERE{{$item->id}}')">{{__('text.word_print')}}</button>
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
                                                <div style=" text-align:center; width:80%; margin-top:3px;">
                                                    {{$student->name}}
                                                </div>
                                            </div>
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                                <div style=" float:left; width:500px;margin-top:3px; text-transform: uppercase;">
                                                    {{ $item->type .' - '.__('text.word_charges')}}
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

                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.word-reference')}}:</div>
                                            <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                                <div style=" float:left; width:300px;margin-top:3px;">
                                                    {{$item->financialTransactionId}}
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
                                        <div style="height:120px; width:95% ; margin-top: 65rem;">
                                            <img width="100%" src="{{$header}}" />
                                        </div>
                                        <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                            {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$item->id}}
                                        </div>
                                        <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" text-align:center; width:80%; margin-top:3px;">
                                                {{$student->name}}
                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:500px;margin-top:3px; text-transform: uppercase;">
                                                {{ $item->type .' - '.__('text.word_charges')}}
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

                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.word-reference')}}:</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:300px;margin-top:3px;">
                                                {{$item->financialTransactionId}}
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
                        </div>
                        <!-- ------------------------------- -->


                    </div>
                @empty
                    <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                        <p>No Payments where found </p>
                    </div>
                @endforelse
            </div>
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