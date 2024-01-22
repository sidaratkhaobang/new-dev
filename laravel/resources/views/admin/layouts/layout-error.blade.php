<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name') }}</title>

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">
    <!-- END Icons -->
    @stack('styles')
    <!-- Stylesheets -->
    <!-- Fonts and Dashmix framework -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;600;700&display=swap"
          rel="stylesheet">
    {{-- <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/dashmix.min.css') }}"> --}}
    <link rel="stylesheet" id="css-main" href="{{ asset('css/dashmix/main.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/loading.css') }}">

    <style>
        .error-status {
            font-size: 166px;
        }
        .error-message {
            font-size: 46px;
        }
        .error-message-2 {
            font-size: 16px;
        }
    </style>
    @stack('custom_styles')
    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->
    <!-- END Stylesheets -->
</head>

<body>
@include('admin.components.spinner')
<div id="page-container" class="page-header-fixed">
    <!-- Header -->
    <header id="page-header">
        <!-- Header Content -->
        <div class="content-header">
            <!-- Left Section -->
            <div class="space-x-1">
                <div class="content-header bg-white-5" style="width: 250px !important; padding-left: 0;" >
                    <!-- Logo -->
                    <a class="fw-semibold text-white tracking-wide" href="{{ route('admin.home') }}">
                        <img src="{{ asset('images/logo_website.png') }}" class="logo" style="width: 100%;">
                    </a>
                    <!-- END Logo -->
                </div>
            </div>
            <!-- END Left Section -->

            <!-- Right Section -->
            <div class="space-x-1">
                @include('admin.layouts.notifications')
                @include('admin.layouts.user-menu')
            </div>
            <!-- END Right Section -->
        </div>
        <!-- END Header Content -->
    </header>
    <!-- END Header -->

    <!-- Main Container -->
    <main id="main-container" class="justify-content-center align-self-center" >
        <!-- Page Content -->
        <div class="content content-main">
            <!-- Your Block -->
            @yield('content')
            <!-- END Your Block -->
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

</div>
<!-- END Page Container -->
<script src="{{ asset('assets/js/dashmix.app.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
<script src="{{ mix('js/app.js') }}"></script>
@stack('scripts')

</body>

</html>
