@extends('teacher.layout')

@section('section')

<div class="container m-t-5">

    <div class="row mt-5">

        <div class="col-6">
            <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{route('user.subject.note.store', [$subject_info->id, $subject_info->subject_id])}}">
                <!-- @crsf -->
                <div class="col-md-6">
                    <input type="file" name="file" placeholder="Choose file" id="file">
                    @error('file')
                    <div class="alert alert-danger mt-2 mb-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <button class="btn btn-md btn-primary mx-3" type="submit" id="upload_note">Upload Note</button>
                </div>
                @csrf
            </form>
        </div>
    </div>


    <!-- notes -->
    <div class="col-sm-12 mt-5">

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Upload date</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notes as $k=>$note)
                        <tr>
                            <td>{{ $k+1 }}</td>
                            <td>{{ $note->note_name}} </td>
                            <td>{{date('jS F Y', strtotime($note->created_at))}}</td>

                            <td style="float: right;">
                                <a href="{{asset('storage/SubjectNotes/'. $note->note_path)}}" class="btn btn-xs btn-success m-3"> <i class="fa fa-download"> Download</i>
                                </a>|
                                <a onclick="event.preventDefault();
                                            document.getElementById('publish').submit();" class=" btn btn-primary btn-sm m-3"><i class="fa fa-eye"> Publish </i></a>
                                <form id="publish" action="{{route('user.subject.note.publish', $note->id)}}" method="POST" style="display: none;" role="form">
                                    @method('PUT')
                                    @csrf
                                </form>
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