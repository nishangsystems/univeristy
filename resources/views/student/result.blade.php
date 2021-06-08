@extends('student.layout')
@section('section')
    @php
        $user = \Auth::user()
    @endphp
    <div class="card-body">
        <div id="table table-responsive" class="table-editable">

            <table class="table table-bordered table-responsive-md table-striped text-center">
                <thead>
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
                @foreach($subjects  as $subject)
                   <tr>
                       <td class="text-center" >{{$subject->name}}</td>
                       @foreach($seqs as $seq)
                           <td class="text-center">{{\App\Helpers\Helpers::instance()->getScore($seq->id, $subject->id, $user->class(\App\Helpers\Helpers::instance()->getYear())->id,\App\Helpers\Helpers::instance()->getYear(), $user->id)}}</td>
                       @endforeach
                   </tr>
                @endforeach
                <tr>
                    <td class="text-center" ><span class="d-none">Z</span> Total</td>
                    @foreach($seqs as $seq)
                        <td class="text-center">{{  $user->totalScore($seq->id, \App\Helpers\Helpers::instance()->getYear()) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="text-center" ><span class="d-none">Z</span> Average</td>
                    @foreach($seqs as $seq)
                        <td class="text-center">{{ $user->averageScore($seq->id, \App\Helpers\Helpers::instance()->getYear())}}</td>
                    @endforeach
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
