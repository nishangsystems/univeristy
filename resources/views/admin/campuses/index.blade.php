@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <div class="content-panel">
            @if(auth()->user()->campus_id == null)
                <div class="py-3 container">
                    <a href="{{route('admin.campuses.create')}}" class="btn btn-sm btn-primary text-capitalize">{{__('text.add_campus')}}</a>
                </div>
            @endif
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-capitalize">{{__('text.word_name')}}</th>
                        <th class="text-capitalize">{{__('text.word_address')}}</th>
                        <th class="text-capitalize">{{__('text.word_contact')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($campuses as $cps)
                        @php($k = 1)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $cps->name }}</td>
                            <td>{{ $cps->address }}</td>
                            <td>{{ $cps->telephone }}</td>
                            <td>
                                @if(auth()->user()->campus_id == null || auth()->user()->campus_id == $cps->id)
                                    <a href="{{route('admin.campuses.edit', $cps->id)}}" class="btn btn-sm btn-primary text-capitalize">{{__('text.word_edit')}}</a>
                                    <a href="{{route('admin.campuses.programs', $cps->id)}}" class="btn btn-sm btn-success text-capitalize">{{__('text.word_programs')}}</a>
                                @endif
                                @if($cps->students()->count() == 0)
                                    <!-- <a href="{{route('admin.campuses.delete', $cps->id)}}" class="btn btn-sm btn-danger text-capitalize">{{__('text.word_delete')}}</a> -->
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
