@extends('frontend.layouts.app')

@section('content')

    <div class="py-6">
        <div class="container">
            <div class="row">
                <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                    <div class="bg-white rounded shadow-sm p-4 text-left">
                        <h1 class="h3 fw-600">{{ translate('Forgot Password?') }}</h1>
                        <p class="mb-4 opacity-60">{{ translate('Enter your email address to recover your password.') }}
                        </p>
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group">

                                @if (addon_is_activated('otp_system'))
                                    <div class="form-group phone-form-group mb-1">
                                        <input type="tel" id="phone-code"
                                            class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                            value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                                    </div>

                                    <input type="hidden" name="country_code" value="">

                                    <div class="form-group email-form-group mb-1 d-none">
                                        <input type="email"
                                            class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                            value="{{ old('email') }}" placeholder="{{ translate('Email') }}"
                                            name="email" id="email" autocomplete="off">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group text-right">
                                        <button class="btn btn-link p-0 opacity-50 text-reset" type="button"
                                            onclick="toggleEmailPhone(this)">{{ translate('Use Email Instead') }}</button>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <input type="email"
                                            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                            value="{{ old('email') }}" placeholder="{{ translate('Email') }}"
                                            name="email" id="email" autocomplete="off">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group text-right">
                                <button class="btn btn-primary btn-block" type="submit">
                                    {{ translate('Send Password Reset Link') }}
                                </button>
                            </div>
                        </form>
                        <div class="mt-3">
                            <a href="{{ route('user.login') }}"
                                class="text-reset opacity-60">{{ translate('Back to Login') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection


@section('script')
    <script type="text/javascript">
        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if (country.iso2 == 'bd') {
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            onlyCountries: @php
                echo json_encode(
                    \App\Models\Country::where('status', 1)
                        ->pluck('code')
                        ->toArray(),
                );
            @endphp,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if (selectedCountryData.iso2 == 'bd') {
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function(e) {
            // var currentMask = e.currentTarget.placeholder;

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });

        function toggleEmailPhone(el) {
            if (isPhoneShown) {
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                $('input[name=phone]').val(null);
                isPhoneShown = false;
                $(el).html('{{ translate('Use Phone Instead') }}');
            } else {
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                $('input[name=email]').val(null);
                isPhoneShown = true;
                $(el).html('{{ translate('Use Email Instead') }}');
            }
        }
    </script>
@endsection
