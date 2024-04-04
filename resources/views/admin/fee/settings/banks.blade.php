@extends('admin.layout')

@section('section')
    <!-- page start-->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <div class="row my-3">
                        <div class="tex-capitalize col-sm-4 col-md-3">@lang('text.bank_name')</div>
                        <div class="tex-capitalize col-sm-8 col-md-9">
                            <input type="text" name="name" class="form-control" value="{{ $bank->name??'' }}">
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="tex-capitalize col-sm-4 col-md-3">@lang('text.account_name')</div>
                        <div class="tex-capitalize col-sm-8 col-md-9">
                            <input type="text" name="account_name" class="form-control" value="{{ $bank->account_name??'' }}">
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="tex-capitalize col-sm-4 col-md-3">@lang('text.account_number')</div>
                        <div class="tex-capitalize col-sm-8 col-md-9">
                            <input type="text" name="account_number" class="form-control" value="{{ $bank->account_number??'' }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end my-3">
                        <button type="submit" class="btn btn-xs btn-primary rounded">@lang('text.word_save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
