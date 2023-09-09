@extends('student.layout')
@section('section')
@php
    $header = \App\Helpers\Helpers::instance()->getHeader();
@endphp
    @if($access == true)
        @if (collect($results)->count() > 0)

            <div class="card-body">
                <div id="table table-responsive" class="table-editable">

                    <table class="table table-bordered table-responsive-md table-striped text-center">
                        <thead>
                            <div class="container-fluid px-0">
                                <img src="{{$header}}" alt="" srcset="" class="img w-100">
                            </div>
                            <div class="container-fluid py-3 h4 my-0 text-center text-uppercase border-top border-bottom border-3 border-dark"><b>
                                {{$semester->name .' '.$year->name. ' '.__('text.individual_results_slip') }}
                            </b></div>
                            <div class="container-fluid p-0 my-0 row mx-auto">
                                <div class="col-sm-7 col-md-8 border-right border-left">
                                    <div class="row py-2 border-top border-bottom border-1">
                                        <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_name')}} :</label>
                                        <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$user->name}}</div>
                                    </div>
                                    <div class="row py-2 border-top border-bottom border-1">
                                        <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_program')}} :</label>
                                        <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$class->program->name}}</div>
                                    </div>
                                    <div class="row py-2 border-top border-bottom border-1">
                                        <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_matricule')}} :</label>
                                        <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$user->matric}}</div>
                                    </div>
                                    <div class="row py-2 border-top border-bottom border-1">
                                        <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_level')}} :</label>
                                        <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{ $class->level->level}}</div>
                                    </div>
                                </div>
                                <div class="col-sm-5 col-md-4 border">
                                    @foreach($grading as $grd)
                                        <span class="d-flex flex-wrap py-2 fs-3">
                                            <span class="col-2">{{($grd->grade ?? '')}}</span>
                                            <span class="col-2">{{':'.($grd->lower ?? '')}}</span>
                                            <span class="col-3">{{'-'.($grd->upper ?? '').'%  '}}</span>
                                            <span class="col-2">{{($grd->weight ?? '')}}</span>
                                            <span class="col-3">{{'  '.($grd->remark ?? '')}}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <tr class="text-uppercase">
                                <th class="text-center" >#</th>
                                <th class="text-center" >{{__('text.word_code')}}</th>
                                <th class="text-center" >{{__('text.word_course')}}</th>
                                <th class="text-center" >ST</th>
                                <th class="text-center" >MV</th>
                                <th class="text-center" >{{__('text.word_module').' / '.$ca_total}}</th>
                                <th class="text-center" >{{__('text.word_module') .' / 100'}}</th>
                                <th class="text-center" >{{__('text.word_grade')}}</th>
                                <th class="text-center" >{{__('text.word_remarks')}}</th>
                            </tr>
                        </thead>
                        <tbody class="text-uppercase text-left">
                            @php
                                $k = 1;
                            @endphp
                            @foreach($results as $subject)
                                @if (!$subject == null)
                                @php
                                    $grade = $grading->filter(function($elt)use($subject){ 
                                        $total = $subject['ca_mark']*5; 
                                        return ($total >= $elt->lower) && ($total <= $elt->upper);
                                    })->first();
                                @endphp
                                <tr class="border-top border-bottom border-secondary border-dashed">
                                    <td class="border-left border-right border-light">{{$k++}}</td>
                                    <td class="border-left border-right border-light">{{$subject['code']}}</td>
                                    <td class="border-left border-right border-light">{{$subject['name']}}</td>
                                    <td class="border-left border-right border-light">{{$subject['status']}}</td>
                                    <td class="border-left border-right border-light">{{$ca_total}}</td>
                                    <td class="border-left border-right border-light">{{$subject['ca_mark']}}</td>
                                    <td class="border-left border-right border-light">{{$subject['ca_mark']*5}}</td>
                                    <td class="border-left border-right border-light">{{$grade->grade}}</td>
                                    <td class="border-left border-right border-light">{{$grade->remark}}</td>
                                </tr>
                                @endif
                            @endforeach
                            <tr class="border border-secondary text-capitalize h4 fw-bolder">
                                <td colspan="2" class="text-center">{{__('text.total_courses_attempted')}} : <span class="px-3">{{count($results)}}</span></td>
                                <td colspan="7" class="text-center">{{__('text.total_courses_passed')}} : <span class="px-3">{{collect($results)->where('ca_mark', '>=', 10)->count()}}</span></td>
                            </tr>
                            <tr class="border border-secondary text-capitalize h4 fw-bolder" style="font-size: medium; font-weight: 500;">
                                <td colspan="3" class="border-0">
                                    <!-- <span class="d-flex">{{__('text.total_credits_attempted')}} : {{$gpa_data['sum_cv']}}</span>
                                    <span class="d-flex">{{__('text.gpa_credits_attempted')}} : {{$gpa_data['gpa_cv']}}</span> -->
                                </td>
                                <td colspan="7" class="border-0">
                                    <!-- <span class="d-flex">{{__('text.total_credits_earned')}} : {{$gpa_data['sum_cv_earned']}}</span>
                                    <span class="d-flex">{{__('text.gpa_credits_earened')}} : {{$gpa_data['gpa_cv_earned']}}</span> -->
                                    <span class="d-flex py-3">{{__('text.semester_gpa')}} : {{number_format($gpa_data['gpa'], 2)}}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        @else
            <div class="py-3 mt-5 text-center h4">
                {{__('text.no_results_phrase')}}
            </div>
        @endif
    @else
        <div class="text-center py-3 h4 alert-danger ">
            {{trans('text.fee_access_phrase', ['amount'=>$min_fee, 'action'=>'access your results'])}}
        </div>
    @endif
@endsection
