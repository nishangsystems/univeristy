@extends('teacher.layout')

@section('section')

<div class="container mt-5">
    @if($notes != null)
    <div class="row mb-5">
        <!-- <div class="col-md-6">
            <h4><b>Subject Notes for {{$notes[0]->name}}</b></h4>
        </div> -->
        <div class="col-md-6">
            <a class="btn btn-md btn-primary" href="{{route('user.subject.show', $id)}}">Add Note</a>
        </div>
    </div>
    @foreach($notes as $key => $note)
    <div class="row">
        <div class="col-8 well well-sm">
            <h4 class="pl-5"><a href="{{asset('storage/SubjectNotes/'. $note->note_path)}}"> {{$note->note_name}}
                </a></h4>
            <h4>
            </h4>
            <p class=" pl-5">Uploaded date: {{date('jS F Y', strtotime($note->created_at))}}</p>

        </div>
    </div>
    @endforeach
    @endif
    @if($notes == null)
    <div class="alert alert-danger" role="alert">Sorry! You have not uploaded any Note for this Subject <a href="{{route('user.subject.show', $id)}}">Click to add note</a></div>
    @endif
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