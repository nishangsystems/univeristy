@extends('student.layout')

@section('section')

<div class="container">
    <!-- notes -->
    <div class="col-sm-12">
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
                            <td> <a href="{{asset('storage/SubjectNotes/'. $note->note_path)}}" class="btn btn-xs btn-success m-3"> <i class="fa fa-download"> Download</i>
                                </a></td>
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

<style scoped>
    a:link {
        text-decoration: none;
        color: black;
    }

    a:hover {
        color: blue;
    }
</style>
@endsection