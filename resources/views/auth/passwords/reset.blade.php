@extends('frontend.layouts.app')

@section('content')
<div class="py-6">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                <div class="bg-white rounded shadow-sm p-4 text-left">
                    <h1 class="h3 fw-600">{{ translate('Reset Password') }}</h1>
                    <p class="mb-4 opacity-60">{{ translate('Enter your email address and new password and confirm password.')}} </p>
                    <form method="POST" action="{{ route('password.update.new') }}" class="form-password">
                        @csrf

                        <div class="form-group">
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ translate('Email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="code" type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{ $email ?? old('code') }}" placeholder="{{ translate('Code')}}" required autofocus>

                            @if ($errors->has('code'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('code') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('New Password') }}"  id="password" onkeyup='check();' required>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group password-icon">
                            <input type="password" class="form-control" placeholder="{{ translate('Confirm Password') }}" name="password_confirmation" id="password_confirm" onkeyup='check();' required>
                            <i class="bi bi-eye-slash" id="togglePassword"></i>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary btn-block" id="btn_submit">
                                {{ translate('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">

        window.addEventListener("DOMContentLoaded", function () {
            const togglePassword = document.querySelector("#togglePassword");

            togglePassword.addEventListener("click", function (e) {
                // toggle the type attribute
                const type = password.getAttribute("type") === "password" ? "text" : "password";
                const type_c = password_confirm.getAttribute("type") === "password" ? "text" : "password";
                password.setAttribute("type", type);
                password_confirm.setAttribute("type", type_c);
                // toggle the eye / eye slash icon
                this.classList.toggle("bi-eye");
            });
        });

        var check = function() {
            if (document.getElementById('password').value == document.getElementById('password_confirm').value) {
                document.getElementById("password").classList.remove('password_error');
                document.getElementById("password_confirm").classList.remove('password_error');
                document.getElementById("btn_submit").disabled = false;
            } else {
                document.getElementById("password").classList.add('password_error');
                document.getElementById("password_confirm").classList.add('password_error');
                document.getElementById("btn_submit").disabled = true;
            }
        }
    </script>
@endsection