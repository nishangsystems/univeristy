@extends('admin.layout')
@section('section')
<div class="container-fluid h-screen d-flex flex-column justify-content-center">
    <div class="col-sm-10 col-md-8 mx-auto my-4 py-4">
        <h2 class="text-dark fw-bolder text-center py-2">Promotion Approval</h2>
        <div class="py-2">
            @foreach(\App\Models\PendingPromotion::all() as $pp)
                <form class="d-flex justify-content-between border-bottom border-top bg-light py-1 my-1" action="{{route('admin.students.approve_promotion')}}">
                    <input type="hidden" name="pending_promotion" value="{{$pp->id}}">
                    <div class=" col-sm-9">Pending promotion from {{$classes[$pp->from_class]}} to {{$classes[$pp->to_class]}} on {{$pp->created_at}}</div>
                    <input type="submit" value="approve" class="btn btn-sm btn-default">
                </form>
            @endforeach
            @if(count(\App\Models\PendingPromotion::all()) == 0)
                <div class="py-2 text-center bg-light text-dark fs-1 fw-bold">0 pending promotions available</div>
            @endif
        </div>
    </div>
</div>
@endsection
@section('script')

@endsection