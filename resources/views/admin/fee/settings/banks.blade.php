@extends('admin.layout')

@section('section')
    <!-- page start-->
    <div class="col-sm-12">
        <div class="d-flex justify-content-end py-3">
            <a href="{{ route('admin.banks') }}" class="btn btn-sm btn-success rounded">@lang('text.create_new_bank')</a>
        </div>
        <div class="card">
            <div class="card-header text-center heading text-capitalize">
                @if(($bank??null) == null)
                    <span>@lang('text.create_new_bank')</span>
                @else
                    <span>@lang('text.edit_bank', ['bank'=>$bank->name??''])</span>
                @endif
            </div>
            <div class="card-body">
                <form method="post">
                    @csrf
                    <div class="row my-3">
                        <div class="tex-capitalize col-sm-4 col-md-3 text-capitalize">@lang('text.bank_name')</div>
                        <div class="tex-capitalize col-sm-8 col-md-9">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $bank->name??'') }}">
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="tex-capitalize col-sm-4 col-md-3 text-capitalize">@lang('text.account_name')</div>
                        <div class="tex-capitalize col-sm-8 col-md-9">
                            <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $bank->account_name??'') }}">
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="tex-capitalize col-sm-4 col-md-3 text-capitalize">@lang('text.account_number')</div>
                        <div class="tex-capitalize col-sm-8 col-md-9">
                            <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $bank->account_number??'' ) }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end my-3">
                        <button type="submit" class="btn btn-xs btn-primary rounded">@lang('text.word_save')</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="my-5 py-2">
            <table class="table table-light">
                <thead class="text-capitalize">
                    <th>#</th>
                    <th>@lang('text.word_bank')</th>
                    <th>@lang('text.account_name')</th>
                    <th>@lang('text.account_number')</th>
                    <th></th>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach($banks as $key => $bank)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $bank->name??null }}</td>
                            <td>{{ $bank->account_name??null }}</td>
                            <td>{{ $bank->account_number??null }}</td>
                            <td>
                                <a href="{{ route('admin.banks', $bank->id) }}" class="btn btn-xs btn-primary rounded">@lang('text.word_edit')</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
