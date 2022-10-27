@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <p class="text-muted">
           <a href="{{route('admin.result_release.create')}}" class="btn btn-info btn-xs text-capitalize">{{__('text.add_release')}}</a>
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="hidden-table-info">
                    <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_batch')}}</th>
                        <th>{{__('text.word_sequence')}}</th>
                        <th>{{__('text.word_status')}}</th>
                        <th>{{__('text.word_date')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($releases as $k=>$release)
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $release->batch->name }}</td>
                            <td>{{ $release->sequence->name }}</td>
                            <td>{{ \Carbon\Carbon::now()->isBefore($release->start_date)?'pending':(\Carbon\Carbon::now()->isAfter($release->end_date)?'passed':'active') }}</td>
                            <td>{{ $release->start_date->format('d/m/Y')." to ".$release->end_date->format('d/m/Y') }}</td>
                            <td class="d-flex justify-content-end align-items-center">
                                <a class="btn btn-xs btn-success" href="{{route('admin.result_release.edit',[$release->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a> |
                                <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
                                <form id="delete" action="{{route('admin.result_release.destroy',$release->id)}}" method="POST" style="display: none;">
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
