@extends('student.layout')
@section('section')
    @php
        
    @endphp
    <div class="card-body">
        <div id="table table-responsive" class="table-editable">

            <table class="table table-bordered table-responsive-md table-striped text-center">
                <thead>
                    <div class="container-fluid px-0">
                        <img src="{{url('/assets/images/header.jpg')}}" alt="" srcset="" class="img w-100">
                    </div>
                    <div class="container-fluid py-3 h4 my-0 text-center text-uppercase border-top border-bottom border-3 border-dark"><b>
                        {{\App\Helpers\Helpers::instance()->getSemester(auth()->user()->program_id)->name .' '.\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name. ' '.__('text.individual_results_slip') }}
                    </b></div>
                    <div class="container-fluid p-0 my-0 row mx-auto">
                        <div class="col-sm-7 col-md-8 border-right border-left border-dark">
                            <div class="row py-2 border-top border-bottom border-1 border-dark">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_name')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$user->name}}</div>
                            </div>
                            <div class="row py-2 border-top border-bottom border-1 border-dark">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_program')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{\App\Models\ProgramLevel::find($user->program_id)->program->name}}</div>
                            </div>
                            <div class="row py-2 border-top border-bottom border-1 border-dark">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_matricule')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{$user->matric}}</div>
                            </div>
                            <div class="row py-2 border-top border-bottom border-1 border-dark">
                                <label for="" class="text-capitalize fw-bold h5 col-sm-12 col-md-4">{{__('text.word_level')}} :</label>
                                <div class="col-sm-12 col-md-8 h4 text-uppercase fw-bolder">{{__('text.word_level').' '. \App\Models\ProgramLevel::find($user->program_id)->level->level}}</div>
                            </div>
                        </div>
                        <div class="col-sm-5 col-md-4 ">
                            <span class="d-flex flex-wrap"></span>
                        </div>
                    </div>
                    <tr>
                        <th style="width: 50px" class="text-center"></th>
                        <th class="text-center" colspan="{{$seqs->count()/2}}">Name : {{$user->name}}</th>
                        <th class="text-center" colspan="{{$seqs->count()/4}}">Matricule : {{$user->matric}}</th>
                        <th class="text-center" colspan="{{$seqs->count()/4}}">Class : {{$user->class(\App\Helpers\Helpers::instance()->getYear())->name}}</th>
                    </tr>
                    <tr>
                        <th class="text-center" >Sequences <br>  Students</th>
                        @foreach($seqs as $seq)
                            <th class="text-center">{{$seq->name}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection
