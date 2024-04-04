@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <div class="content-panel">
            
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
                                <a href="{{route('admin.fee_banks', $cps->id)}}" class="btn btn-sm btn-primary text-capitalize">{{__('text.word_programs')}}</a>
                                <a href="{{route('admin.fee_settings', $cps->id)}}" class="btn btn-sm btn-success text-capitalize">{{__('text.fee_settings')}}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
