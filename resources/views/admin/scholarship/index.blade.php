@extends('admin.layout')
@section('section')
<div class="col-sm-12">
    <div class="col-sm-12">
        <div class="mb-3 d-flex justify-content-start">
            <h4 class="font-weight-bold">Available Scholarships</h4>
        </div>
        <div class="text-muted mb-3 d-flex justify-content-end">
            <a href="{{route('admin.scholarship.create')}}" class="btn btn-info btn-xs">Add Scholarship</a>
        </div>
    </div>
    @if($scholarships != null)
    <div class="content-panel">
        @foreach($scholarships as $scholarship)
        <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
            <div>
                <div>
                    <h4 class="font-weight-bold">Name: {{$scholarship->name}}</h4>
                </div>
                <h6 class="font-weight-bold">Amount: {{$scholarship->amount}} FCFA</h6>
                <h6 class="font-weight-bold">Scholarship Type: {{$scholarship->type}}</h6>
                @if($scholarship->status == 1)
                <h6 class="font-weight-bold">Status: Active</h6>
                @endif
                @if($scholarship->status != 1)
                <h6 class="font-weight-bold">Status: Inactive</h6>
                @endif
            </div>
            <div class="d-inline-flex">
            </div>
        </div>
        @endforeach
    </div>
    @endif
    @if($scholarships == null)
    <div class="content-panel">
        <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
            <p>No Scholarships where found, Click <b>Add Scholarship</b> to add new Scholarship</p>
        </div>
    </div>
    @endif
</div>
@endsection