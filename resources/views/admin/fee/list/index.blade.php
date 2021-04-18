@extends('admin.layout')
@section('section')
    <div class="col-sm-12">
        <div class="text-muted mb-3 d-flex justify-content-end">
            <a href="{{route('admin.fee.list.create', $unit->id)}}" class="btn btn-info btn-xs">Add Fee Listing</a>
        </div>

        <div class="content-panel">
           @forelse($lists as $item)
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <div>
                        <div>{{$item->name}}</div>
                        <h4 class="font-weight-bold">{{$item->amount}} FCFA</h4>
                    </div>
                    <div class="d-inline-flex">
                        <a href="{{route('admin.fee.list.edit', [$unit->id, $item->id])}}" class="btn m-2 btn-sm btn-primary text-white">Edit</a>

                        <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-2">Delete</a>
                        <form id="delete" action="{{route('admin.fee.list.destroy',[$unit->id, $item->id])}}" method="POST" style="display: none;">
                            @method('DELETE')
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            @empty
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <p>No Fee Listing where found, Click <b>Add Fee Listing</b> to add new listing</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
