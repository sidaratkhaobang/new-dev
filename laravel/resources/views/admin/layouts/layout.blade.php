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
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;600;700&display=swap" rel="stylesheet">
    {{-- <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/dashmix.min.css') }}"> --}}
    <link rel="stylesheet" id="css-main" href="{{ asset('css/dashmix/main.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/loading.css') }}">

    @stack('custom_styles')
    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->
    <!-- END Stylesheets -->
    <style>
        /* html {
            zoom: 70% !important;
        } */
        .modal-backdrop.fade.show {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</head>

<body>
@include('admin.components.spinner')
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed">

    <nav id="sidebar" aria-label="Main Navigation">
        <!-- Side Header -->
        <div class="bg-header-light">
            <div class="content-header bg-white-5">
                <!-- Logo -->
                <a class="fw-semibold text-white tracking-wide" href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/logo_website.png') }}" class="logo" style="width: 100%;">
                </a>
                <!-- END Logo -->
            </div>
            <hr class="logo-line">
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
                <button type="button" class="btn btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
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
        <div class="">
            <div class="content content-full" style="padding-top: 0px; padding-bottom: 0px;">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-start align-items-sm-center my-2 my-sm-3">
                    <div class="d-flex justify-content-sm-start align-items-sm-center flex-grow-1 flex-sm-row">
                        <h1 class="fs-3 fw-semibold me-3 mb-0">
                            @yield('page_title', 'blank')
                            <span class="page-title-line ms-2">
                                @yield('page_title_no', '')
                            </span>
                        </h1>
                        @yield('history') &emsp; @yield('page_title_sub')
                    </div>
                    <div class="d-flex justify-content-sm-end align-items-sm-center flex-grow-1 flex-sm-row">
                        @yield('btn_1') &emsp; @yield('btn_2')
                    </div>
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
<script src="{{ asset('packages/doubleScrollbar/jquery.doubleScroll.js') }}"></script>

<script src="{{ mix('js/app.js') }}"></script>
<script>
    $(document).ready(function () {
        /* $('.db-scroll').doubleScroll({
            resetOnWindowResize: true
        }); */

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "inherit");
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "auto");
        })
    });

    $('.number-format').toArray().forEach(function (field) {
        new Cleave(field, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand'
        });
    });

    $('.tel-format').toArray().forEach(function (field) {
        new Cleave(field, {
            delimiter: '-',
            blocks: [3, 3, 4],
            uppercase: true
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

    function clearForm(selector) {
        $(selector).find("input[type=text], input[type=number], input[type=email], input[type=password], textarea").val("");
        $(selector).find("select").val(null).trigger('change');
        $(selector).find("input[type=radio]").prop("checked", false);
        $(selector).find("input[type=select]").prop("checked", false);
    }

    function appendHidden(selector, name, value) {
        $("<input>").attr({
            name: name,
            type: "hidden",
            value: value
        }).appendTo(selector);
    }

    function __log(item = null) {
        console.log(item)
    }

    function transaction() {
        $('#transaction').modal('show');
    }

    function number_format(number, digit = 2) {
        number = isNaN(number) ? 0 : number;
        return (new Intl.NumberFormat([], {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
        }).format(number));
    }

    function delay(callback, ms) {
        var timer = 0;
        return function () {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    function disableInputs(inputs) {
        inputs.forEach(input => {
            input.setAttribute('readonly', 'true');
            input.setAttribute('disabled', 'true');
        });
    }

    function empty(data) {
        // Check if data is a number or boolean, and return false as they're never considered empty
        if (typeof data === 'number' || typeof data === 'boolean') {
            return false;
        }
        
        // Check if data is undefined or null, and return true as they're considered empty
        if (typeof data === 'undefined' || data === null) {
            return true;
        }

        // Check if data has a length property (e.g. strings, arrays) and return true if the length is 0
        if (typeof data.length !== 'undefined') {
            return data.length === 0;
        }

        // Check if data is an object and use Object.keys() to determine if it has any enumerable properties
        if (typeof data === 'object') {
            return Object.keys(data).length === 0;
        }

        // Return false for any other data types, as they're not considered empty
        return false;
    };

    function bind_on_change_radio(name, cb) {
        $("input[type=radio][name=" + name + "]").on("change", function () {
            var val = $("input[type=radio][name=" + name + "]:checked").val();
            cb(parseInt(val, 10));
        });
        $("input[type=radio][name=" + name + "]:checked").trigger("change");
    }

    function set_select2(selector, value, label) {
        selector.append(new Option(label, value, true, true)).trigger('change');
    }

    //     Vue Component
    Vue.component('input-number-format-vue', {
        template: '<input v-bind:name="name" class="form-control" @input="updateValue"></input>',
        props: {
            name: '',
            options: Object,
            value: null,
        },
        mounted() {
            let input = $(this.$el);
            if (this.value) {
                input.val(this.value).trigger('change')
            }
            new Cleave(input, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
            })
        },
        methods: {
            updateValue(event) {
                this.$emit('input', event.target.value);
            }
        }
    });

    Vue.component('input-tel-format-vue', {
        template: '<input v-bind:name="name" class="form-control" @input="updateValue">',
        props: {
            name: '',
            options: Object,
            value: null,
        },
        mounted() {
            let input = $(this.$el);
            if (this.value) {
                input.val(this.value).trigger('change')
            }
            new Cleave(input, {
                blocks: [3, 3, 4],
                numericOnly: true,
                delimiter: '-',
            })

        },
        methods: {
            updateValue(event) {
                this.$emit('input', event.target.value);
            }
        }
    });

    Vue.component('input-citizen-format-vue', {
        template: '<input v-bind:name="name" class="form-control" @input="updateValue">',
        props: {
            name: '',
            options: Object,
            value: null,
        },
        mounted() {
            let input = $(this.$el);
            if (this.value) {
                input.val(this.value).trigger('change')
            }
            new Cleave(input, {
                numeral: true,
                numeralThousandsGroupStyle: 'none'
            })

        },
        methods: {
            updateValue(event) {
                this.$emit('input', event.target.value);
            }
        }
    });
</script>
@stack('pre_scripts')
@stack('scripts')

</body>

</html>
