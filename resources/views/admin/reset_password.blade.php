@extends('admin.layout')
@section('section')
<div class="py-2">
    <form method="post" class="form-panel">
        @csrf
        <div class="row text-capitalize my-2">
            <label class="col-sm-3">{{__('text.word_username')}}</label>
            <div class="col-sm-9">
                <label class="form-control">

                        {{auth()->user()->email  ?? auth()->user()->name}}
                </label>
            </div>
        </div>
        <div class="row text-capitalize my-2">
            <label class="col-sm-3">{{__('text.current_password')}}</label>
            <div class="col-sm-9">
                <input type="password" class="form-control" name="current_password" required>
            </div>
        </div>
        <div class="row text-capitalize my-2">
            <label class="col-sm-3">{{__('text.new_password')}}</label>
            <div class="col-sm-9">
                <input type="password" class="form-control" name="new_password" required id="current_password">
            </div>
        </div>
        <div class="row text-capitalize my-2">
            <label class="col-sm-3">{{__('text.confirm_new_password')}}</label>
            <div class="col-sm-9">
                <input type="password" class="form-control" name="new_password_confirmation" required id="confirm_password" onkeyup="check_match()">
            </div>
        </div>
        <div class="d-flex justify-content-end my-2">
            <button class="btn btn-sm btn-primary" type="submit" id="submit_button">{{__('text.word_reset')}}</button>
        </div>
    </form>
</div>
@endsection
@section('script')
<script>
    function check_match() {
        let new_pass = $('#current_password').val();
        if (new_pass == '' || new_pass == null) {
            alert('Fill new password first');
            $("#confirm_password").val('');
            $("#current_password").toggleClass('border');
            $("#current_password").toggleClass('border-danger');
        }

        if ($('#confirm_password').val() != $('#current_password')) {
            $('#confirm_password').addClass('border');
            $('#confirm_password').addClass('border_danger');
        }
        if($('#confirm_password').val() == $('#current_password')){
            $('#confirm_password').remove('border');
            $('#confirm_password').remove('border_danger');
            $('#submit_button').removeClass('hidden')
        }
    }
</script>
@endsection