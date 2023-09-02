@extends('parents.layout')
@section('section')

    <div class="col-sm-12">
        <div class="row ">
            @foreach ($contacts as $contact)
                <div class="col-sm-10 mx-auto col-md-5 col-lg-3 text-center alert alert-info border rounded-md py-4 px-2">
                    <h4 class="">{{ $contact->name??'' }}</h4>
                    <h6 class="my-2">{{ $contact->position??'' }}</h6>
                    <h5 class="my-2">{{ $contact->contact??'' }}</h5>
                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('script')
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection