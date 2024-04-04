@extends('admin.layout')

@section('section')
    <!-- page start-->
    <div class="col-sm-12">
        @if(($program??null) != null)
            <form class="card" method="POST">
                @csrf
                <div class="card-header header text-center text-capitalize">@lang('text.set_program_bank')</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 py-2">
                            <input type="hidden" name="school_unit_id" value="{{ $program->id??'' }}" required>
                            <input type="text" name="" value="{{ $program->name??'' }}" readonly class="form-control" id="">
                            <div class="text-capitalize text-secondary">@lang('text.word_program')</div>
                        </div>
                        <div class="col-md-5 py-2">
                            <select name="bank_id" required class="form-control" class="form-control">
                                <option value=""></option>
                                @foreach($banks as $key => $bank)
                                    <option value="{{ $bank->id??'' }}"  {{ old('bank_id', $bank->id) == ($program_bank->id??null) ? 'selected' : '' }}>{{ $bank->name??'---' }}</option>
                                @endforeach
                            </select>
                            <div class="text-capitalize text-secondary">@lang('text.word_bank')</div>
                        </div>
                        <div class="col-md-2 py-2">
                            <button class="text-capitalize btn-primary btn rounded form-control" type="submit">@lang('text.word_save')</btn>
                        </div>
                    </div>
                </div>
            </form>
        @endif
        <div class="my-3 py-2">
            <table class="table">
                <thead class="text-capitalize">
                    <th>#</th>
                    <th>@lang('text.word_program')</th>
                    <th>@lang('text.word_bank')</th>
                    <th></th>
                </thead>
                <tbody>
                    @php
                        $counter = 1;
                    @endphp
                    @foreach($programs as $key => $prog)
                    @php
                        $bank = $prog->banks($campus->id)->first();
                    @endphp
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ ($prog->parent->parent->name??'').'::'.($prog->parent->name??'').'::'.$prog->name??'' }}</td>
                            <td>
                                <span class="text-capitalize">@lang('text.bank_name')</span>:: {{ $bank->name??'not set' }} <br>
                                <span class="text-capitalize">@lang('text.account_name')</span>:: {{ $bank->account_name??'not set' }} <br>
                                <span class="text-capitalize">@lang('text.account_number')</span>:: {{ $bank->account_number??'not set' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.fee_banks', [$campus->id??null, $prog->id??null]) }}" class="btn btn-xs btn-primary rounded">@lang('text.word_edit')</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
