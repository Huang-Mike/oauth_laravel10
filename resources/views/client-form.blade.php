@extends('main')

<head>
    <link rel="icon" type="image/x-icon" href="{{
            (isset($config['customized']['logo'])) ?
            asset('storage/clients/logo/' . $config['customized']['logo']) : asset('img/logo.png')
        }}">
    <title>
        @if (!$error)
            {{ __("client-form.$routeName") }} | {{ __("client-name.$client->name") }}
        @else
            {{ json_decode($error, true)[0]['message'] }}
        @endif
    </title>
</head>

@section('content')
    <link href="{{ asset('css/client-form.css') }}" rel="stylesheet">

    @if (!$error)
        <div class="outer-div d-flex align-items-center no-select">
            <div class="content-area container d-flex justify-content-center align-items-center">
                {{-- Get OTP sms --}}
                <div id="getOPTArea" class="inner-div">
                    <div class="m-3 text-center">
                        <img class="logo mb-3"
                            src=@if (isset($config['customized']['logo'])) {{ asset('storage/clients/logo/' . $config['customized']['logo']) }}
                        @else
                            {{ asset('img/logo.png') }} @endif>
                        <p class="message_title mb-3">
                            {{ isset($config['message']['title']) ? $config['message']['title'] : __('client-form.welcome', ['clientName' => __("client-name.$client->name")]) }}
                        </p>
                        <p class="message_content mb-3">
                            {{
                                isset($config['message']['title']) ? $config['message']['content'] :
                                __('client-form.continue', [
                                    'attribute' => __("client-form.$routeName"),
                                    'clientName' => __("client-name.$client->name")
                                ])
                            }}
                        </p>
                    </div>
                    <div class='m-3'>
                        <form id="registerForm" method="POST">
                            @csrf

                            @if ($routeName != 'login')
                                <div class="mb-2">
                                    <label for="name" class="form-label">@lang('client-form.name')</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            @endif

                            @if ($routeName != 'login' || $config['auth']['primary_key'] == 'email')
                                <div class="mb-2">
                                    <label for="email" class="form-label">@lang('client-form.email')</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            @endif
                            @if ($routeName != 'login' || $config['auth']['primary_key'] == 'phone')
                                <div class="mb-2">
                                    <label for="phone" class="form-label">@lang('client-form.phone')</label>
                                    <div class='d-flex'>
                                        <select class="form-select w-auto" aria-label="Default select example"
                                            name='isdCode'>
                                            @foreach (config('isdCode') as $key => $value)
                                                <option value="{{ $value['isd_code'] }}">
                                                    {{ $value['country_code'] . '(' . $value['isd_code'] . ')' }}</option>
                                            @endforeach
                                        </select>
                                        <input type="tel" class="form-control" name="phone" minlength="10"
                                            maxlength="10" pattern="[0-9]+" required>
                                    </div>
                                </div>
                            @endif

                            <!-- If this client's verify type is password,  -->
                            @if ($config['auth']['verify_type'] == 'password')
                                <div class="mb-2">
                                    <label for="password" class="form-label">@lang('client-form.password')</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>

                                @if ($routeName != 'login' && $config['auth']['verify_type'] == 'password')
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">@lang('client-form.confirm_password')</label>
                                        <input type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                @endif
                            @endif

                            <div class="d-grid mb-3 mt-3">
                                <button id="submitBtn" type="submit" class="btn btn-primary">
                                    <div id="loading" class="loading d-none">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>{{ $config['auth']['verify_type'] == 'password' ? __('client-form.processing') : __('client-form.sending') }}
                                    </div>
                                    <span id="submitBtn_text">
                                        {{ $config['auth']['verify_type'] == 'password' ? __('client-form.submit') : __('client-form.get_otp') }}
                                    </span>
                                </button>
                            </div>

                            @if($routeName != 'bind')
                                <div class="text-center m-2">
                                    <a href="{{ $exchangeTypeUrl }}">
                                        <span class="link-secondary">
                                            @if($routeName == 'login')
                                                @lang('client-form.unregistered')
                                            @elseif($routeName == 'register')
                                                @lang('client-form.registered')
                                            @endif
                                        </span>
                                    </a>
                                </div>
                            @endif

                            <div class="text-center m-2">
                                <a href="{{ route('change.lang', ['locale' => (app()->getLocale() == 'en-US') ? 'zh-TW' : 'en-US']) }}">
                                    <span class="link-secondary">
                                        {{ (app()->getLocale() == 'en-US') ? '中文' : 'Engilsh' }}
                                    </span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Enter OTP sms --}}
                @if ($config['auth']['verify_type'] == 'otp')
                    <div id="enterOTPArea" class="inner-div d-none">
                        <div class='m-3'>
                            <form id="otpForm" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="otp" class="form-label">@lang('client-form.enter_otp')</label>
                                    <input type="text" class="form-control" name="otp" minlength="6" maxlength="6"
                                        pattern="[0-9]+" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button id="otpBtn" type="submit" class="btn btn-primary">
                                        <div id="loading" class="loading d-none">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>@lang('client-form.sending')
                                        </div>
                                        <span id="otpBtn_text">@lang('client-form.verify')</span>
                                    </button>
                                </div>
                                <div class="m-3 d-flex justify-content-center">
                                    <div id="countDown"></div>
                                    <div id="resend_sms" class="link-secondary dis-click" style="cursor: pointer;">@lang('client-form.resend_otp')</div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Alert modal -->
    <div class="modal fade" id="alert_model" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alert_title">@lang('client-form.notice')</h5>
                </div>
                <div id="alert_content" class="modal-body text-center"></div>
                <div class="modal-footer">
                    <button id='alert_confirm' type="button" class="btn btn-primary"
                        data-bs-dismiss="modal">@lang('client-form.ok')</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var modal    = $('#alert_model'),
            modalObj = new bootstrap.Modal(modal),
            error    = JSON.parse(@json($error)) ?? [],
            formUrl  = "{{ $formUrl }}",
            verifyOtpUrl = "{{ $verifyOtpUrl }}",
            verifyType = "{{ $config['auth']['verify_type'] ?? null }}",
            primaryKey = "{{ $config['auth']['primary_key'] ?? null }}";
    </script>
    <script src="{{ asset('js/client-form.js') }}"></script>
@endsection
