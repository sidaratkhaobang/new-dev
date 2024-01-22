<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">

    <!-- Stylesheets -->
    <!-- Fonts and Dashmix framework -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/dashmix.min.css') }}">

    <style>
        .full-screen {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: -webkit-flex;
            display: flex;
            -webkit-flex-direction: column
            /* works with row or column */
            
            flex-direction: column;
            -webkit-align-items: center;
            align-items: center;
            -webkit-justify-content: center;
            justify-content: center;
            text-align: center;
        }
        .icon-custom {
            color: blue;
        }
    </style>
    {{-- <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/dashmix.min.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/main.css') }}"> --}}
</head>

<body>
    <div id="page-container" class="full-screen">
        <div>
            <h1 class="icon-custom text-primary"><i class="far fa-hand-peace"></i></h1>
            <br>
            <h2 class="text-dark">ขอบคุณที่ใช้บริการ</h2>
            <h2 class="text-dark">กรุณาตรวจสอบสถานะการชำระเงิน  กับพนักงานขาย</h2>
        </div>
    </div>
</body>
</html>
