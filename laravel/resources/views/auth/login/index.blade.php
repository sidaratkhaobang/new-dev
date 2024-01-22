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
</head>

<body>
    <div id="page-container">
        <main id="main-container">
            <div class="row g-0 justify-content-center bg-body-dark">
                <div class="hero-static col-sm-10 col-md-10 col-xl-3 d-flex align-items-center p-2 px-sm-0">
                    <div class="block block-rounded block-transparent block-fx-pop w-100 mb-0 overflow-hidden bg-image"
                        style="background-image: url('assets/media/photos/photo20@2x.jpg');">
                        <div class="row g-0">
                            <div class="col-md-12 order-md-1 bg-body-extra-light">
                                <div class="block-content block-content-full px-lg-5 py-md-5 py-lg-6">
                                    <div class="mb-2 text-center">
                                        <a class="link-fx fw-bold fs-1" href="{{ route('login') }}">
                                            <span class="text-dark">Smart</span> <span class="text-primary">Car</span>
                                        </a>
                                        <p class="text-uppercase fw-bold fs-sm text-muted">{{ __('auth.login') }}</p>
                                    </div>
                                    <form class="js-validation-signin" action="{{ route('auth.check') }}"
                                        method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <input type="text"
                                                class="form-control @if ($errors->any()) is-invalid @endif"
                                                id="username" name="username" placeholder="{{ __('auth.username') }}"
                                                required>
                                        </div>
                                        <div class="mb-4">
                                            <input type="password"
                                                class="form-control @if ($errors->any()) is-invalid @endif"
                                                id="password" name="password" placeholder="{{ __('auth.password') }}"
                                                required>
                                        </div>
                                        @if ($errors->any())
                                            <div class="text-danger text-center">
                                                @foreach ($errors->all() as $error)
                                                    <p class="error-message" style="line-height: 26px;">
                                                        {!! $error !!}</p>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="mb-4">
                                            <button type="submit" class="btn w-100 btn-hero btn-primary">
                                                <i class="fa fa-fw fa-sign-in-alt opacity-50 me-1"></i>
                                                {{ __('auth.login') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
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
