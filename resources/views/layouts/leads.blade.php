<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @yield('head')
    <link rel="stylesheet" href="{{ asset('vendor/leads/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/fullcalendar.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/fullcalendar.print.css') }}" media="print">
    <link rel="stylesheet" href="{{ asset('vendor/leads/bootstrap-extend.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/master-style.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/horizontal-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/fontawesome/css/v4-shims.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/source-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/source-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/leads/tasmiya-shell.css') }}">
    <style>
        body { padding-top: 70px; background: #F4F7F6; }
        .layout-top-nav .content-wrapper { margin-top: 0; margin-left: auto; margin-right: auto; min-height: calc(100vh - 70px); }
        .content { max-width: none; }
        .modal { z-index: 1060; }
        .modal-backdrop { z-index: 1050; }
        .swal2-container { z-index: 1100; }
        .lead-original-message { white-space: pre-wrap; overflow-wrap: anywhere; }
        .validation-errors { white-space: normal; }
        #followup-form input[type=checkbox] { position: static !important; opacity: 1 !important; width: auto; height: auto; appearance: auto; margin-right: 6px; }
        :focus-visible { outline: 2px solid #1683bd; outline-offset: 3px; }
        @media(max-width:575px) { #lead-calendar{min-height:0!important} #lead-calendar .fc-scroller{height:auto!important;overflow:visible!important} .fc-toolbar .fc-left,.fc-toolbar .fc-right,.fc-toolbar .fc-center{float:none;display:block;margin-bottom:10px} .fc-toolbar h2{float:none} .fc-toolbar .fc-button-group{float:none;display:inline-block} }
    </style>
    <link rel="stylesheet" href="{{ asset('vendor/leads/tasmiya-theme.css') }}?v={{ filemtime(public_path('vendor/leads/tasmiya-theme.css')) }}">
</head>
<body class="hold-transition skin-green layout-top-nav leads-theme">
    @include('partials.navigation')
    <main id="lead-workspace">@yield('content')</main>
    <script src="{{ asset('vendor/leads/jquery.js') }}"></script>
    <script src="{{ asset('vendor/leads/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/leads/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/leads/moment.js') }}"></script>
    <script src="{{ asset('vendor/leads/fullcalendar.js') }}"></script>
    <script src="{{ asset('vendor/leads/select2.js') }}"></script>
    <script>$.fn.select2.defaults.set('theme', 'bootstrap');</script>
    <script src="{{ asset('vendor/leads/sweetalert2.js') }}"></script>
    @vite('resources/js/layout-interactions.js')
    @yield('scripts')
</body>
</html>
