@extends('student.printable')
@section('section')
    <table>
        <thead>
            <tr class="py-3 h4 my-0">
                <th colspan="5" class="text-center">
                    {{$semester->name .' '.$year->name. ' '.__('text.CA') }}
                </th>
            </tr>
            <tr>
                <th colspan="5" class="px-0 py-0">
                    <table>
                        <tr class="border-top border-bottom">
                            <td>
                                <table>
                                    <tr>
                                        <td class="text-capitalize fw-bold h5 col-sm-4">{{__('text.word_name')}} :</td>
                                        <td class="col-sm-8 h5 text-uppercase fw-bolder">{{$user->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-capitalize fw-bold h5 col-sm-4">{{__('text.word_program')}} :</td>
                                        <td class="col-sm-8 h5 text-uppercase fw-bolder">{{$class->program->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-capitalize fw-bold h5 col-sm-4">{{__('text.word_matricule')}} :</td>
                                        <td class="col-sm-8 h5 text-uppercase fw-bolder">{{$user->matric}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-capitalize fw-bold h5 col-sm-4">{{__('text.word_level')}} :</td>
                                        <td class="col-sm-8 h5 text-uppercase fw-bolder">{{ $class->level->level}}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <div class=" px-0 py-0">
                                    <table class="h6">
                                        <thead class="text-primary">
                                            <th>Grd</th>
                                            <th colspan="2">Range</th>
                                            <th>Wt</th>
                                            <th>Rmk</th>
                                        </thead>
                                        @foreach($grading as $grd)
                                            <tr class="pb-1 pt-0" style="text-decoration: none;">
                                                <td class="py-0">{{($grd->grade ?? '')}}</td>
                                                <td class="py-0">{{($grd->lower ?? '')}}</td>
                                                <td class="py-0">{{($grd->upper ?? '')}}</td>
                                                <td class="py-0">{{($grd->weight ?? '')}}</td>
                                                <td class="py-0">{{($grd->remark ?? '')}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr class="text-uppercase">
                <th class="text-center py-0" >{{__('text.course_code')}}</th>
                <th class="text-center py-0" >{{__('text.course_title')}}</th>
                <th class="text-center py-0" >ST</th>
                <th class="text-center py-0" >CV</th>
                <th class="text-center py-0" >{{__('text.CA').'/'.$ca_total}}</th>
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
                <td colspan="2" class="text-center border-left border-right border-light">{{__('text.total_courses_attempted')}} : <span class="px-3">{{count($results)}}</span></td>
                <td colspan="7" class="text-center border-left border-right border-light"></td>
            </tr>
        </tbody>
    </table>
@endsection