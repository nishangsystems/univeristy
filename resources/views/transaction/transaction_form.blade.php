@extends('layouts.base')


@section('section')
    <div class="">
        @if( !Session::has('transaction_response') )
            <div id="payment-form-wrapper">
                <form action="{{route('make_payments')}}"  method="POST" id="payment-form" >
                    @csrf
                    <div class="d-flex flex-column align-items-center justify-content-center mb-4 form-group">
                        <img src="{{asset('icons/mtn-mm-logo-generic-mtn-mobile-money-logo.svg')}}"
                             alt="mtn momo logo">
                        <span>MTN Momo</span>
                    </div>
                    <div class="form-group">

                    </div>

                    <input type="text" placeholder="672345338" name="tel" value="{{old('tel') ?? '672345338'}}">
                    <input type="hidden" placeholder="amount" name="amount" value="{{old('amount') ?? 100}}">
                    <input type="hidden" placeholder="payment purpose" name="payment_purpose"
                           value="{{old('payment_purpose') ?? 'Test'}}">
                    <input type="hidden" placeholder="Year id" name="year_id" value="{{old('year_id') ?? '2'}}">
                    <input type="hidden" placeholder="url" name="url" value="{{route('make_payments')}}">
                    <input type="hidden" placeholder="student id" name="student_id"
                           value="{{old('student_id') ?? '8'}}">
                    <input type="hidden" placeholder="reference" name="reference"
                           value="{{old('reference') ?? 'Test reference'}}">
                    <input type="hidden" placeholder="redirect route" name="redirect_route"
                           value="{{old('redirect_route' ) ?? 'payment_form' }}">

                    <button class="btn btn-primary">Make payment</button>

                </form>
            </div>
        @else
            @dump(session('transaction_response'))

            <div id="payment-res-wrapper">
                <div id="payment-initiated"
                     class="@if(session('transaction_response')['transaction_status'] != 'payment initiated') d-none @endif">
                    <h1>Payment request initiated</h1>
                    <p class="sub-heading mb-3">You will receive a payment request on your phone</p>
                    <p class="sub-heading">If you don't dial <strong>*126#</strong> to initiate the payment</p>
                </div>
                <div id="payment-failed"
                     class="@if(session('transaction_response')['transaction_status']  != 'payment failed') d-none @endif">
                    <h1>Transaction failed!</h1>
                    <p class="sub-heading">Oops! Your transaction failed.Please try again</p>
                    <button id="try-again" class="confirm">Try again</button>
                </div>
                <div id="payment-complete"
                     class="@if(session('transaction_response')['transaction_status']  != 'payment completed') d-none @endif">
                    <img src="../assets/icons/check.svg" alt=""/>
                    <h1>Payment successful</h1>
                    <p>You have paid your service </p>
                </div>
            </div>
        @endif
    </div>

    <style>
        .d-none {
            display: none !important;
        }
    </style>

@endsection

@section('script')
{{--    <script defer>--}}

{{--        const form = document.getElementById('payment-form');--}}
{{--        form.addEventListener('submit', initiateTransaction);--}}

{{--          function initiateTransaction(event) {--}}
{{--            event.preventDefault();--}}
{{--            try {--}}


{{--                const url = form.url.value;--}}

{{--                const formData = new FormData();--}}
{{--                formData.append(tel)--}}

{{--                console.log(formData)--}}
{{--                console.log(url)--}}
{{--                // fetch(url, {--}}
{{--                //     method: 'POST',--}}
{{--                //     headers:{--}}
{{--                //         "Content-Type":"multipart/form-data"--}}
{{--                //     },--}}
{{--                //     body: formData--}}
{{--                // }).then((response) => console.log(response));--}}
{{--                const data = {--}}
{{--                    tel: form.tel.value,--}}
{{--                    amount: form.amount.value,--}}
{{--                    reference: form.reference.value,--}}
{{--                    year_id: form.year_id.value,--}}
{{--                    student_id: form.student_id.value,--}}
{{--                    payment_purpose: form.payment_purpose.value,--}}
{{--                    redirect_route: form.redirect_route.value,--}}
{{--                    _token: form._token.value--}}
{{--                }--}}

{{--                // return ;--}}
{{--                // data['tel'] =--}}
{{--                // Default options are marked with *--}}
{{--                const response = fetch(url, {--}}
{{--                    method: "POST", // *GET, POST, PUT, DELETE, etc.--}}
{{--                    mode: "cors", // no-cors, *cors, same-origin--}}
{{--                    cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached--}}
{{--                    credentials: "same-origin", // include, *same-origin, omit--}}
{{--                    headers: {"Content-Type": "multipart/form-data"},--}}
{{--                    // redirect: "follow", // manual, *follow, error--}}
{{--                    // referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url--}}
{{--                    body:  new FormData(event.target), // body data type must match "Content-Type" header--}}
{{--                }).then((response)=>{--}}
{{--                    console.log("response",response)--}}
{{--                });--}}
{{--                //--}}
{{--                // console.log(response);--}}
{{--            }catch (e) {--}}
{{--                console.log("error");--}}
{{--                console.log(e);--}}
{{--            }--}}
{{--        }--}}





{{--    </script>--}}
@endsection