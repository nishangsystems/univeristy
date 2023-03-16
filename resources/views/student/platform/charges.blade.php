@extends('student.layout')
@section('section')
@php
    $c_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $c_semester = \App\Helpers\Helpers::instance()->getSemester(auth('student')->user()->_class()->id);
    if($purpose == 'TRANSCRIPT'){
        $year = $c_year;
    }
@endphp
<div class="py-3">
        @if ($year_id == null)
            <form method="get">
                <div class="row my-2">
                    <label class="text-capitalize col-sm-3">{{__('text.academic_year')}}</label>
                    <div class="col-sm-9"><select class="form-control" name="year_id" required>
                        <option></option>
                        @foreach (\App\Models\Batch::all() as $batch)
                            <option value="{{$batch->id}}" {{$batch->id == $c_year ? 'selected' : ''}}>{{$batch->name}}</option>
                        @endforeach
                    </select></div>
                </div>
                @if ($purpose == 'RESULTS')
                    <div class="row my-2">
                        <label class="text-capitalize col-sm-3">{{__('text.word_semester')}}</label>
                        <div class="col-sm-9"><select class="form-control" name="semester_id" required>
                            <option>----------</option>
                            @foreach (auth('student')->user()->_class()->program->background->semesters as $sem)
                                <option value="{{$sem->id}}">{{$sem->name}}</option>
                            @endforeach
                        </select></div>
                    </div>
                @endif
                <div class="d-flex justify-content-end my-2 py-2">
                    <button type="submit" class="btn btn-sm btn-primary">{{__('text.word_proceed')}}</button>
                </div>
            </form>
        @else
            @if ($purpose == 'RESULTS' && (\App\Models\Charge::where(['year_id'=>$year_id, 'semester_id'=>$semester->id, 'student_id'=>auth('student')->id(), 'type'=>'RESULTS'])->count() > 0))
                <div class="text-center py-3 h4 text-info">{{__('text.dublicate_charges_attempt')}}</div>
            @elseif ($purpose == 'PLATFORM' && (\App\Models\Charge::where(['year_id'=>$year_id, 'student_id'=>auth('student')->id(), 'type'=>'PLATFORM'])->count() > 0))
                <div class="text-center py-3 h4 text-info">{{__('text.dublicate_charges_attempt')}}</div>
            @else
                <!-- check if student has already paid the request  -->
                <div class="container">

                    <div class="text-center text-danger h3">
                        @switch($purpose)
                            @case('PLATFORM')
                                {{__('text.platform_payments_template_text', ['purpose'=>__('text.PLATFORM_CHARGES'), 'amount'=>$amount, 'year'=>\App\Models\Batch::find($year_id)->name, 'semester'=>''])}}
                                @break
                            @case('RESULTS')
                                {{__('text.platform_payments_template_text', ['purpose'=>__('text.SEMESTER_RESULT_CHARGES'), 'amount'=>$amount, 'year'=>\App\Models\Batch::find($year_id)->name, 'semester'=>$semester->name])}}
                                @break
                            @case('TRANSCRIPT')
                                {{__('text.platform_payments_template_text', ['purpose'=>__('text.TRANSCRIPT_APPLICATION_CHARGES'), 'amount'=>$amount,  'year'=>\App\Models\Batch::find($year_id)->name, 'semester'=>''])}}
                                @break
                        
                            @default
                                
                        @endswitch
                    </div>
                    
                    <form method="post" action="{{route('student.charge.pay')}}" id="poster-form" class="mt-5 py-4 px-3 bg-light" style="border-radius: 1rem;">
                        <!-- SET REQUIRED HIDDEN INPUT FIELDS HERE -->
                        @csrf
                        <input type="hidden" name="payment_purpose" value="{{$purpose}}">
                        <input type="hidden" name="student_id" value="{{auth('student')->id()}}">
                        <input type="hidden" name="payment_id" value="{{$payment_id}}">
                        <input type="hidden" name="amount" value="{{$amount}}">
                        <input type="hidden" name="year_id" value="{{$year_id}}">
                        <input type="hidden" name="semester_id" value="{{$semester_id ?? ''}}">
                        <input type="hidden" name="channel" id="p-channel" value="{{$semester_id ?? ''}}"><!-- holds the payment channel to be used for payment -->
                        <div class="row my-4 py-3">
                            <label class="col-sm-3 text-capitalize">{{__('text.word_amount')}}</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="tel" name="tel" value="{{$amount}}" readonly>
                            </div>
                        </div>
                        <div class="row my-4 py-3">
                            <label class="col-sm-3 text-capitalize">{{__('text.payment_number')}}</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="tel" name="tel" value="{{auth('student')->user()->phone}}">
                            </div>
                        </div>
                        <div class="row my-4 py-3">
                            <label class="col-sm-3 text-capitalize"></label>
                            <div class="col-sm-9">
                                <h3 class="text-dark text-capitalize">{{__('text.pay_with')}} : </h3>
                                <div class="flex justify-content-center text-center my-4 py-2">
                                    <span class="mx-3 text-center d-inline-block">
                                        <button type="submit" class="d-block border-0 btn-white rounded-md mb-4" onclick="event.preventDefault(); $('#p-channel').val('mtnmomo'); $('#poster-form').submit()">
                                            <img class="img img-responsive rounded d-block" src="{{url('public/assets/images/mtn_momo.jpg')}}" style="height: 8rem; width: 12rem">
                                        </button>
                                        <span class="h4 fw-bolder">{{__('text.mtn_mobile_money')}}</span>
                                    </span>
                                    <span class="mx-3 text-center d-inline-block">
                                        <button type="submit" class="d-block border-0 btn-white rounded-md mb-4" onclick="event.preventDefault(); $('#p-channel').val('orangemoney'); $('#poster-form').submit()">
                                            <img class="img img-responsive rounded" src="{{url('public/assets/images/Orange_Money.jpg')}}" style="height: 8rem; width: 12rem">
                                        </button>
                                        <span class="h4 fw-bolder">{{__('text.orange_money')}}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    
                </form>
                </div>
            @endif
        @endif
    </div>
@endsection