@extends('student.layout')
@section('section')
    @php
        $user = \Auth::user()
    @endphp
    <div class="card-body">
        <div id="table table-responsive" class="table-editable">

            <table class="table table-bordered table-responsive-md table-striped text-center">
                <thead>
                    <div class="container-fluid">
                        <img src="{{url('/assets/images/header.jpg')}}" alt="" srcset="" class="img w-100">
                    </div>
                    <div class="container-fluid py-3 h5 fw-bolder text-center">
                        {{\App\Helpers\Helpers::instance()->getSemester(auth()->user()->program_id)->name .' '.\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAcademicYear())->name. ' '.__('text.word_results') }}
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
