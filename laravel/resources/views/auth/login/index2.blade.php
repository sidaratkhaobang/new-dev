<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/dashmix.min.css') }}">
    <style>
        .bg-custom {
            background-size: 100% 100%;
            background-position: right;
        }
        .pre-wrap {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <div id="page-container">

    <!-- Main Container -->
    <main id="main-container">
        <!-- Page Content -->
        <div class="bg-custom" style="background-image: url('{{ asset('images/bg_img.jpg') }}');">
        <div class="row g-0">
            <!-- Main Section -->
            <div class="hero-static {{ $pdpa ? 'col-md-6' : 'col-md-12 justify-content-center' }} d-flex align-items-center bg-body-extra-light">
                <div class="p-3 {{ $pdpa ? 'w-100' : 'w-50' }}" style="margin-top: -150px;" >
                    <!-- Header -->
                    <div class="mb-3 text-center">
                        <img src="{{ asset('images/new_logo.png') }}" style="max-width: 150px; width: 100%;" >
                        <br>
                        <a class="link-fx fw-bold fs-1" href="{{ route('login') }}">
                            <span class="text-dark">Smart</span><span class="text-primary">Car</span>
                        </a>
                        <br><br>
                        <p class="text-uppercase fw-bold fs-sm text-muted">{{ __('auth.login') }}</p>
                    </div>
                    <!-- END Header -->

                    <div class="row g-0 justify-content-center">
                    <div class="col-sm-8 col-xl-6">
                        <form class="js-validation-signin" action="{{ route('auth.check') }}" method="POST">
                            @csrf
                            <div class="py-3">
                                <div class="mb-4">
                                <input type="text" class="form-control form-control-lg form-control-alt @if ($errors->any()) is-invalid @endif" 
                                    id="username" name="username" placeholder="{{ __('auth.username') }}">
                                </div>
                                <div class="mb-4">
                                <input type="password" class="form-control form-control-lg form-control-alt @if ($errors->any()) is-invalid @endif" 
                                    id="password" name="password" placeholder="{{ __('auth.password') }}">
                                </div>

                                @if ($errors->any())
                                    <div class="text-danger text-center">
                                        @foreach ($errors->all() as $error)
                                            <p class="error-message m-0" style="line-height: 26px;">
                                                {!! $error !!}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="mb-4">
                                <button type="submit" class="btn w-100 btn-lg btn-hero btn-primary">
                                    <i class="fa fa-fw fa-sign-in-alt opacity-50 me-1"></i> {{ __('auth.login') }}
                                </button>
                                <p class="mt-3 mb-0 d-lg-flex justify-content-lg-between">
                                    {{-- <a class="btn btn-sm btn-alt-secondary d-block d-lg-inline-block mb-1" href="op_auth_reminder.html">
                                        <i class="fa fa-exclamation-triangle opacity-50 me-1"></i> Forgot password
                                    </a>
                                    <a class="btn btn-sm btn-alt-secondary d-block d-lg-inline-block mb-1" href="op_auth_signup.html">
                                        <i class="fa fa-plus opacity-50 me-1"></i> New Account
                                    </a> --}}
                                </p>
                            </div>
                        </form>
                    </div>
                    </div>
                    <!-- END Sign In Form -->
                </div>
            </div>
            <!-- END Main Section -->

            <!-- Meta Info Section -->
            @if ($pdpa)
                <div class="hero-static col-md-6 d-none d-md-flex justify-content-md-center text-md-center">
                    <div class="py-5 px-5" style="max-height: 100vh; overflow: auto;" >
                        <p class="display-6 fw-bold mb-1">
                            คำประกาศเกี่ยวกับความเป็นส่วนตัว
                        </p>
                        <br>
                        <p class="mb-0 text-start pre-wrap">
                            {{ $pdpa->description_th }}
                        </pre>
                        <br>
                        <p class="mb-0 text-start pre-wrap">
                            {{ $pdpa?->description_en }}
                        </pre>
                    </div>
                </div>
            @endif
            <!-- END Meta Info Section -->
        </div>
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->
    </div>
    <!-- END Page Container -->

    <script src="{{ asset('assets/js/dashmix.app.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // if length > 0 remove class is-invalid
            $("#password").keyup(function() {
                if ($(this).val().length >= 1) {
                    $("#password").removeClass("is-invalid");
                    $("#password").addClass("is-valid");
                    $("p").remove(".error-message");
                }
            });
            $("#username").keyup(function() {
                if ($(this).val().length >= 1) {
                    $("#username").removeClass("is-invalid");
                    $("#username").addClass("is-valid");
                    $("p").remove(".error-message");
                }
            });

            // if length = 0 remove class is-valid
            $('#username').blur(function() {
                if ($(this).val().length === 0 && $("#username").hasClass("is-valid")) {
                    $("#username").removeClass("is-valid");
                }
            });

            $('#password').blur(function() {
                if ($(this).val().length === 0 && $("#password").hasClass("is-valid")) {
                    $("#password").removeClass("is-valid");
                }
            });
        });
    </script>
</body>
</html>
