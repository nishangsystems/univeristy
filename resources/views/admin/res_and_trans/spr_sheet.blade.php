@extends('admin.layout')
@section('style')
    <style>
        .score{
            width: 15px;
        }
        table{
            width: 100% !important;
        }

        #dataTable{
            max-width: 278mm;
        }

        @media print {
            @page {
                size: landscape;
                margin: 1in; /* You can adjust the margins as needed */
            }

            button.no-print, form.no-print, div.no-print {
                display:none !important;
            }

            .compressed-table{
                margin: 0 auto;
                padding: 0;
                overflow-x: auto;
            }
        }

        .title{
            transform: rotate(270deg);
            padding: 10px 0px;
            height: 150px;
        }

    </style>

@endsection

@section('action')
    <button class="btn btn-primary no-print" onclick="window.print()">Print</button>
@endsection
@section('section')
    @if(!request()->has('class_id'))
        <form method="post" target="new" class="no-print">
            @csrf
            <div class="row my-3 py-3 text-capitalize">
                <div class=" col-sm-6 col-md-5 col-lg-5 px-2">
                    <label for="">{{__('text.word_class')}}</label>
                    <div>
                        <select class="chosen-select form-control" name="class_id" required  data-placeholder="search class by name...">
                            <option selected class="text-capitalize">{{__('text.select_class')}}</option>
                            @forelse(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                                <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class=" col-sm-6 col-md-4 col-lg-4 px-2">
                    <label for="">{{__('text.word_semester')}}</label>
                    <div>
                        <select name="semester_id" id="" class="form-control rounded" required>
                            <option value=""></option>
                            @foreach(\App\Models\Semester::all() as $sem)
                                <option value="{{$sem->id}}">{{$sem->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class=" col-sm-6 col-md-3 col-lg-3 px-2">
                    <label for="">{{__('text.word_year')}}</label>
                    <div>
                        <select name="year_id" id="" class="form-control rounded" required>
                            <option value=""></option>
                            @foreach(\App\Models\Batch::all() as $year)
                                <option value="{{$year->id}}">{{$year->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="my-2 px-0 mx-0 d-flex justify-content-end no-print"><input type="submit" class="btn btn-sm text-capitalize btn-primary rounded" value="{{__('text.build_spread_sheet')}}"></div>
        </form>
    @else
        @php
            $k = 1;
        @endphp
       <div class="d-flex justify-content-center">
           <div class="my-2" id="dataTable">
               <img src="{{\App\Helpers\Helpers::instance()->getHeader()}}" alt="" class="w-100">
               <div class="text-center py-2">
                   <div class="d-flex justify-content-center">
                       <h4 class="text-decoration text-capitalize no-print"><b>
                               {{ $_title }}
                           </b></h4>
                   </div>

                   <div class="d-flex justify-content-center overflow-auto">
                       <table class="compressed-table">
                           <thead class="text-capitalize">
                           <tr class="border-top border-bottom border-secondary">
                               <th class="border-left border-right border-secondary" colspan="2"></th>
                               @foreach ($courses as $course)
                                   <th class="border-left border-right border-secondary title "   colspan="4"><p> {!! strip_tags($course->subject->name) !!}</p><p>{{$course->subject->code}} </p></th>
                               @endforeach
                           </tr>
                           <tr class="border-top border-bottom border-secondary">
                               <th class="border-left border-right border-secondary">#</th>
                              @if(request('with_names') == 1)
                                   <th class="border-left border-right border-secondary" style="width: 180px">{{__('Name')}}</th>
                              @endif
                               <th class="border-left border-right border-secondary">{{__('text.word_matricule')}}</th>
                               @foreach ($courses as $course)
                                   <th class="border-left border-right border-secondary">{{__('text.CA')}}</th>
                                   <th class="border-left border-right border-secondary">{{__('text.EX')}}</th>
                               <!-- <th class="border-left border-right border-secondary">{{__('text.word_exams')}}</th> -->
                                   <th class="border-left border-right border-secondary">{{__('text.TT')}}</th>
                               <!-- <th class="border-left border-right border-secondary">{{__('text.word_total')}}</th> -->
                                   <th class="border-left border-right border-secondary">{{__('text.GR')}}</th>
                               <!-- <th class="border-left border-right border-secondary">{{__('text.word_grade')}}</th> -->
                               @endforeach

                           </tr>
                           </thead>
                           <tbody class="text-sm">
                           @foreach($students as $student)
                               @if($student->hasResult(request('class_id'), $year, request('semester_id')))
                                   <tr class="border-top border-bottom border-secondary">
                                       <td class="border-left border-right border-secondary">{{$k++}}</td>
                                       @if(request('with_names') == 1)
                                           <td class="text-left border-left border-right border-secondary">{{$student->name}}</td>
                                       @endif
                                       <td class="border-left border-right border-secondary">{{$student->matric}}</td>
                                       @foreach ($courses as $course)
                                           <td class="border-left border-secondary score">{{$student->ca_score($course->subject->id, request('class_id'), $year, request('semester_id'))}}</td>
                                           <td class="border-left border-right border-info score">{{$student->exam_score($course->subject->id, request('class_id'), $year, request('semester_id'))}}</td>
                                           <td class="border-left border-right border-info score">{{$student->total_score($course->subject->id, request('class_id'), $year, request('semester_id'))}}</td>
                                           <th class="border-right border-secondary score {{$student->total_score($course->subject->id, request('class_id'), $year, request('semester_id')) >= 50 ? 'text-success':'text-danger'}}">{{$student->grade($course->subject->id, request('class_id'), $year, request('semester_id'))}}</th>
                                       @endforeach
                                   </tr>
                               @endif
                           @endforeach
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>
       </div>
    @endif
@endsection
@section('script')
@endsection