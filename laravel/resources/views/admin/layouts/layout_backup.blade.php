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
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/dashmix.min.css') }}">
    {{-- <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/main.css') }}"> --}}
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/loading.css') }}">
    <style>
        html,
        body {
            font-family: 'Sarabun', sans-serif;
        }

        .table td,
        .table th {
            white-space: nowrap;
        }

        .table td.td-break,
        .table th.th-break {
            white-space: normal !important;
        }

        .sticky-col {
            position: -webkit-sticky;
            /* position: sticky; */
            right: 0;
        }

        th.sticky-col {
            background-color: #dfe4f1
        }

        td.sticky-col {
            z-index: 5;
        }

        .table tbody tr td.sticky-col {
            background-color: white;
        }

        .table tbody tr.table-success td.sticky-col {
            background-color: #dee9cd;
        }

        .table-striped tbody tr:nth-of-type(odd) td.sticky-col {
            background-color: #f6f7fb;
        }

        .table-striped tbody tr:nth-of-type(even) td.sticky-col {
            background-color: white;
        }

        .db-scroll::-webkit-scrollbar,
        .doubleScroll-scroll-wrapper::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .db-scroll::-webkit-scrollbar-thumb,
        .doubleScroll-scroll-wrapper::-webkit-scrollbar-thumb {
            background: #D4D4D4;
            border-radius: 30px;
        }

        .db-scroll::-webkit-scrollbar-thumb:hover,
        .doubleScroll-scroll-wrapper::-webkit-scrollbar-thumb:hover {
            background: #B3AFB3;
        }

        .db-scroll::-webkit-scrollbar-track,
        .doubleScroll-scroll-wrapper::-webkit-scrollbar-track {
            background: #F0F0F0;
            border-radius: 30px;
            box-shadow: inset 0px 0px 0px 0px #F0F0F0;
        }

        .badge {
            font-weight: 400 !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 400 !important;
        }

        .badge-custom {
            min-width: 8rem;
            /*display: flex;*/
            /*flex-direction: row;*/
            /*justify-content: center;*/
            /*align-items: center;*/
            /*height: 32px;*/
        }

        .badge-bg-primary {
            border: 1px solid #0665d0;
            background: rgba(6, 101, 208, 0.04);
            color: #0665d0;
        }

        .badge-bg-info {
            border: 1px solid #00adff;
            background: rgba(0, 173, 255, 0.04);
            color: #00adff;
        }

        .badge-bg-warning {
            border: 1px solid #EFB008;
            background: rgba(239, 176, 8, 0.04);
            color: #EFB008;
        }

        .badge-bg-success {
            border: 1px solid #0ab512;
            background: rgba(10, 181, 18, 0.04);
            color: #0ab512;
        }

        .badge-bg-danger {
            border: 1px solid #ef0808;
            background: rgba(239, 8, 8, 0.04);
            color: #ef0808;
        }

        .badge-bg-dark-blue {
            border: 1px solid #0B3E79;
            background: rgba(6, 101, 208, 0.04);
            color: #0B3E79;
        }

        .badge-bg-dark-orange {
            border: 1px solid #F88906;
            background: rgba(6, 101, 208, 0.04);
            color: #F88906;
        }

        .badge-bg-tls-color {
            border: 1px solid #409653;
            background: rgba(64, 150, 83, 0.04);
            color: #409653;
        }

        .badge-bg-secondary {
            border: 1px solid #475569;
            background: rgba(71, 85, 105, 0.04);
            color: #475569;
        }

        .bg-vendor-color {
            border: 1px solid #6f9c40;
            background: rgba(111, 156, 64, 0.04);
            color: #6f9c40;
        }
    </style>
    @stack('custom_styles')
    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->
    <!-- END Stylesheets -->
</head>

<body>
    @include('admin.components.spinner')
    <div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed">

        <nav id="sidebar" aria-label="Main Navigation">
            <!-- Side Header -->
            <div class="bg-header-dark">
                <div class="content-header bg-white-5">
                    <!-- Logo -->
                    <a class="fw-semibold text-white tracking-wide" href="{{ route('admin.home') }}">
                        <span class="smini-visible">
                            SmartCar
                        </span>
                        <span class="smini-hidden">
                            SmartCar
                        </span>
                    </a>
                    <!-- END Logo -->
                </div>
            </div>
            <!-- END Side Header -->

            <!-- Sidebar Scrolling -->
            <div class="js-sidebar-scroll">
                @include('admin.layouts.menus')
            </div>
            <!-- END Sidebar Scrolling -->
        </nav>
        <!-- END Sidebar -->

        <!-- Header -->
        <header id="page-header">
            <!-- Header Content -->
            <div class="content-header">
                <!-- Left Section -->
                <div class="space-x-1">
                    <!-- Toggle Sidebar -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
                    <button type="button" class="btn btn-alt-secondary" data-toggle="layout"
                        data-action="sidebar_toggle">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                    <!-- END Toggle Sidebar -->
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

            <!-- Header Loader -->
            <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
            <div id="page-header-loader" class="overlay-header bg-header-dark">
                <div class="bg-white-10">
                    <div class="content-header">
                        <div class="w-100 text-center">
                            <i class="fa fa-fw fa-sun fa-spin text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Header Loader -->
        </header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container">
            <!-- Hero -->
            <div class="bg-body-light">
                <div class="content content-full" style="padding-top: 8px; padding-bottom: 8px;">
                    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                        <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">@yield('page_title', 'blank') @yield('history') &emsp; @yield('page_title_sub') </h1>
                        
                        @yield('btn-nav')
                        {{-- <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">Pages <a href="#"></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Blank</li>
                            </ol>
                        </nav> --}}
                    </div>
                </div>
            </div>
            <!-- END Hero -->

            <!-- Page Content -->
            <div class="content">
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
    <script src="{{ asset('packages/doubleScrollbar/jquery.doubleScroll.js') }}"></script>

    <script src="{{ mix('js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.db-scroll').doubleScroll({
                resetOnWindowResize: true
            });

            $('.table-responsive').on('show.bs.dropdown', function() {
                $('.table-responsive').css("overflow", "inherit");
            });

            $('.table-responsive').on('hide.bs.dropdown', function() {
                $('.table-responsive').css("overflow", "auto");
            })
        });

        $('.number-format').toArray().forEach(function(field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        });

        $(document).on('shown.bs.modal', function () {
            $('.modal .number-format').toArray().forEach(function (field) {
                new Cleave(field, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand'
                });
            });
        });

        function showLoading() {
            $('.loading-wrapper').addClass('loadingio-spinner');
        }

        function hideLoading() {
            $('.loading-wrapper').removeClass('loadingio-spinner');
        }

        function __d(item = null) {
            console.log(item)
        }

        function transaction() {
            $('#transaction').modal('show');
        }

    </script>

   
    @stack('scripts')

</body>

</html>
