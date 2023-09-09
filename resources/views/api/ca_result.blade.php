@extends('api.layout')
@section('section')
    @php
        $header = \App\Helpers\Helpers::instance()->getHeader();
    @endphp
    @if (collect($results)->count() > 0)

        <div class="card-body">
            <div id="table table-responsive" class="table-editable">

                <table class="table table-bordered table-responsive-md table-striped text-center">
                    <thead>
                    <div class="container-fluid px-0">
                        <img src="{{$header}}" alt="" srcset="" class="img w-100">
                    </div>
                    <div class="container-fluid py-3 h4 my-0 text-center text-uppercase border-top border-bottom border-3 border-dark"><b>
                            {{$semester->name .' '.$year->name. ' '.__('text.CA') }}
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
                        <th class="text-center" >{{__('text.word_code')}}</th>
                        <th class="text-center" >{{__('text.word_course')}}</th>
                        <th class="text-center" >ST</th>
                        <th class="text-center" >CV</th>
                        <th class="text-center" >{{__('text.CA').'/'.$ca_total}}</th>
                    </tr>
                    </thead>
                    <tbody class="text-uppercase text-left">
                    @foreach($results as $subject)
                        <tr class="border-top border-bottom border-secondary border-dashed">
                            <td class="border-left border-right border-light">{{$subject['code']}}</td>
                            <td class="border-left border-right border-light">{{$subject['name']}}</td>
                            <td class="border-left border-right border-light">{{$subject['status']}}</td>
                            <td class="border-left border-right border-light">{{$subject['coef']}}</td>
                            <td class="border-left border-right border-light">{{$subject['ca_mark']}}</td>
                        </tr>
                    @endforeach
                    <tr class="border border-secondary text-capitalize h4 fw-bolder">
                        <td colspan="2" class="text-center">{{__('text.total_courses_attempted')}} : <span class="px-3">{{count($results)}}</span></td>
                        <td colspan="7" class="text-center"></td>
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
@endsection
