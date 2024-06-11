@extends('admin.layout')
@section('section')
    <div class="py-3">
        <div class="container-fluid">
            <div class="py-4">
                <div class="header h4 fw-smeibold text-capitalize">@lang('text.resit_registration_info')</div>
                <div class="row my-3">
                    <span class="text-capitalize text-secondary col-lg-3">{{ __('text.word_count') }}:</span>
                    <div class="col-lg-9">
                        <label class="form-control rounded">{{ $courses->count() }}</label>
                    </div>
                </div>
                <div class="row my-3">
                    <span class="text-capitalize text-secondary col-lg-3">{{ __('text.unit_cost') }}:</span>
                    <div class="col-lg-9">
                        <label class="form-control rounded">{{ $resit_cost??'' }}</label>
                    </div>
                </div>
                <div class="row my-3">
                    <span class="text-capitalize text-secondary col-lg-3">{{ __('text.amount_expected') }}:</span>
                    <div class="col-lg-9">
                        <label class="form-control rounded">{{ ($resit_cost??0)*($courses->count()) }}</label>
                    </div>
                </div>
                <div class="row my-3">
                    <span class="text-capitalize text-secondary col-lg-3">{{ __('text.amount_recieved') }}:</span>
                    <div class="col-lg-9">
                        <label class="form-control rounded">{{ ($resit_cost??0)*($courses->whereNotNull('paid')->count()) + $collected??0 }}</label>
                    </div>
                </div>
                <div class="row my-3">
                    <span class="text-capitalize text-secondary col-lg-3">{{ __('text.word_balance') }}:</span>
                    <div class="col-lg-9">
                        <label class="form-control rounded">{{ ($resit_cost??0)*($courses->whereNull('paid')->count()) -$collected??0 }}</label>
                    </div>
                </div>

            </div>
            <div class="header h4 fw-smeibold text-capitalize">@lang('text.record_payment')</div>
            <form method="POST">
                @csrf
                <div class="row my-3">
                    <span class="text-capitalize text-secondary col-lg-3">{{ __('text.word_amount') }}:</span>
                    <div class="col-lg-9">
                        <input type="number" name="amount" required class="form-control rounded" value="{{ ($resit_cost??0)*($courses->whereNull('paid')->count()) - $collected??0 }}">
                    </div>
                </div>
                <div class="d-flex my-3 justify-content-end">
                    <button class="btn btn-sm btn-primary px-5 rounded">{{ __('text.word_save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection