@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="col-sm-12 mb-5">
        <div class="d-flex justify-content-start ">
            <a id="save" class="btn btn-sm btn-info mx-3 text-capitalize" type="button" href="{{route('admin.boarding_fee')}}">{{__('text.add_boarding_fee')}}</a>
        </div>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_unit')}}</th>
                        <th>{{__('text.word_section')}}</th>
                        <th>{{__('text.word_amount')}}: {{trans_choice('text.new_student', 2)}} ({{__('text.currency_cfa')}})</th>
                        <th>{{__('text.word_amount')}}: {{trans_choice('text.old_student', 2)}} ({{__('text.currency_cfa')}})</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($boarding_fees as $k=>$boarding_fee)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td> {{$boarding_fee->schoolUnitParent->name}}</td>
                        <td> {{$boarding_fee->schoolUnit->name}}</td>
                        <td>{{number_format($boarding_fee->amount_new_student)}}</td>
                        <td>{{number_format($boarding_fee->amount_old_student)}}</td>
                        <td class="d-flex justify-content-end  align-items-center">

                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.boarding_fee.edit',[$boarding_fee->id])}}"><i class="fa fa-edit text-capitalize"> {{__('text.word_edit')}}</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash text-capitalize"> {{__('text.word_delete')}}</i></a>
                            <form id="delete" action="{{route('admin.boarding_fee.destroy',$boarding_fee->id)}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
