@extends('admin.layout')
@php
    $this_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
    $c_year = request('year') ?? $this_year;
@endphp
@section('section')
<div class="col-sm-12">

    <div class="form-panel mb-5 ml-2">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.scholarship.awarded_students')}}">
            <div class="form-group @error('year') has-error @enderror ">
            <div class="col-sm-8"></div>
                <div class="col-sm-2">
                    <select class="form-control text-capitalize" name="year">
                        <option value="">{{__('text.select_year')}}</option>
                        @foreach($years as $key => $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
               
               <div class="col-sm-2 d-flex justify-content-center">
                <label class="control-label mb-2 mr-2"> <button class="btn btn-xs btn-primary" type="submit">{{__('text.get_scholars')}}</button></label>
                </div>
            </div>
            @csrf
        </form>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th>{{__('text.word_program')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th>{{__('text.scholarship_amount')}} </th>
                        <th>{{__('text.word_reason')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($students as $k=>$student)
                    @php
                        $pl = $student->_class($c_year);
                        // dd($students);
                    @endphp
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$student->name}}</td>
                        <td>{{$student->matric}}</td>
                        <td>{{$pl == null ? '': $pl->name()}}</td>
                        <td>{{$student->campus->name}}</td>
                        <td>{{number_format($student->amount)}}</td>
                        <td>{{$student->reason}}</td>
                        <td>
                            <a href="{{route('admin.scholarship.edit', $student->sc_id)}}" class="btn btn-sm btn-primary">{{__('text.word_edit')}}</a>|
                            @if ($student->sc_year == $this_year)
                                <form method="post" action="{{route('admin.scholarship.delete', $student->sc_id)}}">@csrf <button type="submit" class="btn btn-sm btn-danger my-1">{{__('text.word_cancel')}}</button></form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                
            </div>
        </div>
    </div>
</div>
@endsection