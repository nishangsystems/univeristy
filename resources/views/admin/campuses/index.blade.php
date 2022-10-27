@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <div class="content-panel">
            <div class="py-3 container">
                <a href="{{route('admin.campuses.create')}}" class="btn btn-sm btn-primary text-capitalize">add campus</a>
            </div>
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
                        @php($k = 0)
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $cps->name }}</td>
                            <td>{{ $cps->address }}</td>
                            <td>{{ $cps->telephone }}</td>
                            <td>
                                <a href="{{route('admin.campuses.edit', $cps->id)}}" class="btn btn-sm btn-primary text-capitalize">{{__('text.word_edit')}}</a>
                                <a href="{{route('admin.campuses.programs', $cps->id)}}" class="btn btn-sm btn-success text-capitalize">{{__('text.word_programs')}}</a>
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
