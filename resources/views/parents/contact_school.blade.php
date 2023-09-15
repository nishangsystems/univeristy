@extends('parents.layout')
@section('section')

    <div class="col-sm-12">
        <div class="row ">
            @foreach ($contacts as $contact)
                <div class="col-sm-10 mx-auto col-md-5 col-lg-3 text-center border py-4 px-2" style="box-shadow: -1 -1 #eee, 1 1 #eee; border-radius: 1rem;">
                    <h4 class="">{{ $contact->name??'' }}</h4>
                    <h6 class="my-2 text-secondary"><i>{{ $contact->title??'' }}</i></h6>
                    <h5 class="my-2 text-primary">{{ $contact->contact??'' }}</h5>
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