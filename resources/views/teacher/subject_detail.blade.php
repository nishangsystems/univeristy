@extends('teacher.layout')

@section('section')

<div class="container m-t-5">

    <div class="row mt-5">

        <div class="col-6">
            <form class="form-horizontal " role="form" method="POST" enctype="multipart/form-data" action="{{route('user.subject.note.store', [$subject_info->id, $subject_info->subject_id])}}">
                <!-- @crsf -->
                <div class="col-md-5">
                    <input type="file" name="file" placeholder="Choose file" id="file">
                    @error('file')
                    <div class="alert alert-danger mt-2 mb-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 d-flex">
                    <label class="text-capitalize mr-3">{{__('text.word_material')}} </label>
                    <select name="option" id="" class="text-capitalize">
                        <option value="note" selected>{{__('text.word_notes')}}</option>
                        <option value="assignment">{{__('text.word_assignment')}}</option>
                    </select>
                    @error('option')
                    <div class="alert alert-danger mt-2 mb-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-primary mx-3 text-capitalize" type="submit" id="upload_note">{{__('text.word_upload')}}</button>
                </div>
                @csrf
            </form>
        </div>
    </div>


    <!-- notes -->
    <div class="col-sm-12 col-md-11 mt-5">

        <div class="">
            <div class="container-fluid">
                <table  class="table" id="hidden-table-info">
                    <thead class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_type')}}</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.upload_date')}}</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach($notes as $k=>$note)
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $note->type ?? ''}} </td>
                            <td>{{ $note->note_name}} </td>
                            <td>{{date('jS F Y', strtotime($note->created_at))}}</td>

                            <td style="">
                                <a href="{{asset('storage/SubjectNotes/'. $note->note_path)}}" class="btn btn-xs btn-success m-3"> <i class="fa fa-download"> Download</i>
                                </a>|
                                @if($note->status == 0)
                                    <a onclick="event.preventDefault();
                                                document.getElementById('publish').submit();" class=" btn btn-primary btn-sm m-3"><i class="fa fa-eye text-capitalize">{{__('text.word_publish')}}</i></a>
                                    <form id="publish" action="{{route('user.subject.note.publish', $note->id)}}" method="POST" style="display: none;" role="form">
                                        @method('PUT')
                                        @csrf
                                    </form>
                                @else
                                    <a onclick="event.preventDefault();
                                                document.getElementById('unpublish').submit();" class=" btn btn-primary btn-sm m-3"><i class="fa fa-eye text-capitalize">{{__('text.word_unpublish')}}</i></a>
                                    <form id="unpublish" action="{{route('user.subject.note.publish', $note->id)}}?action=unpublish" method="POST" style="display: none;" role="form">
                                        @method('PUT')
                                        @csrf
                                    </form>
                                @endif
                                <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash"> Delete </i></a>
                                <form id="delete" action="{{route('user.subject.note.destroy', $note->id)}}" method="POST" style="display: none;" role="form">
                                    @method('DELETE')
                                    @csrf
                                </form>

                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{$notes->links()}}
                </div>
            </div>
        </div>
    </div>

</div>

<script>

</script>
@endsection