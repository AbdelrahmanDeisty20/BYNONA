<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
@props(['bodyClass'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('dashboard/assets') }}/img/favicon-square.png">
    <link rel="icon" type="image/png" href="{{ asset('dashboard/assets') }}/img/favicon-square.png">
    <title>
        Bynona
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('dashboard/assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('dashboard/assets') }}/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('dashboard/assets') }}/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />

    <style>
        /* RTL Base */
        body.rtl {
            font-family: 'Cairo', 'Roboto', sans-serif !important;
            text-align: right;
            direction: rtl;
        }

        /* LTR Overrides (Ensure symmetry with RTL) */
        body:not(.rtl).g-sidenav-show .main-content {
            margin-left: 17.125rem !important;
            margin-right: 0 !important;
        }
        
        @media (max-width: 1199.98px) {
            body:not(.rtl) .main-content {
                margin-left: 0 !important;
            }
            body:not(.rtl) .sidenav {
                transform: translateX(-17.125rem);
            }
            body:not(.rtl).g-sidenav-show.g-sidenav-pinned .sidenav {
                transform: translateX(0);
            }
        }

        /* Utility Overrides for RTL */
        .rtl .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
        .rtl .me-auto { margin-left: auto !important; margin-right: 0 !important; }
        .rtl .ms-1 { margin-right: 0.25rem !important; margin-left: 0 !important; }
        .rtl .ms-2 { margin-right: 0.5rem !important; margin-left: 0 !important; }
        .rtl .ms-3 { margin-right: 1rem !important; margin-left: 0 !important; }
        .rtl .me-1 { margin-left: 0.25rem !important; margin-right: 0 !important; }
        .rtl .me-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
        .rtl .me-3 { margin-left: 1rem !important; margin-right: 0 !important; }
        
        .rtl .ps-1 { padding-right: 0.25rem !important; padding-left: 0 !important; }
        .rtl .ps-2 { padding-right: 0.50rem !important; padding-left: 0 !important; }
        .rtl .ps-3 { padding-right: 1rem !important; padding-left: 0 !important; }
        .rtl .ps-4 { padding-right: 1.5rem !important; padding-left: 0 !important; }
        .rtl .pe-1 { padding-left: 0.25rem !important; padding-right: 0 !important; }
        .rtl .pe-2 { padding-left: 0.50rem !important; padding-right: 0 !important; }
        .rtl .pe-3 { padding-left: 1rem !important; padding-right: 0 !important; }

        .rtl .text-start { text-align: right !important; }
        .rtl .text-end { text-align: left !important; }
        
        /* Sidebar RTL */
        .rtl .sidenav {
            right: 0 !important;
            left: auto !important;
            border-left: 0;
            border-right: 0;
            overflow-y: auto !important;
        }
        .rtl.g-sidenav-show .sidenav {
            transform: translateX(0);
        }
        .rtl.g-sidenav-hidden .sidenav {
            transform: translateX(17.125rem);
        }
        
        .rtl.g-sidenav-show .main-content {
            margin-right: 17.125rem !important;
            margin-left: 0 !important;
        }
        @media (max-width: 1199.98px) {
            .rtl .main-content {
                margin-right: 0 !important;
            }
            .rtl .sidenav {
                transform: translateX(17.125rem);
            }
            .rtl.g-sidenav-show.g-sidenav-pinned .sidenav {
                transform: translateX(0);
            }
        }
        
        .rtl .dropdown-menu {
            text-align: right !important;
            right: 0 !important;
            left: auto !important;
        }
        .rtl .navbar-nav .nav-item .nav-link i {
            margin-left: 0.5rem !important;
            margin-right: 0 !important;
        }
        
        .rtl .breadcrumb-item + .breadcrumb-item {
            padding-right: 0.5rem;
            padding-left: 0;
        }
        .rtl .breadcrumb-item + .breadcrumb-item::before {
            float: right;
            padding-left: 0.5rem;
            content: var(--bs-breadcrumb-divider, "/");
        }
        
        .rtl .card-header .bg-gradient-primary h6 {
            padding-right: 1rem !important;
            padding-left: 0 !important;
        }
        .rtl .table thead th {
            text-align: right !important;
        }
        .rtl .form-check {
            padding-right: 1.5rem;
            padding-left: 0;
        }
        .rtl .form-check .form-check-input {
            float: right;
            margin-right: -1.5rem;
            margin-left: 0;
        }
        .rtl .form-switch .form-check-input {
            margin-right: -2.5rem;
            margin-left: 0;
        }

        /* Scrollbar RTL Fix */
        .rtl #sidenav-scrollbar {
            overflow: auto !important;
        }
        .rtl .ps__rail-y {
            left: 0 !important;
            right: auto !important;
        }

        /* Card Icons RTL Fix */
        .rtl .card .card-header .icon {
            right: 1rem;
            left: auto;
        }
        .rtl .card .card-header .text-end {
            text-align: left !important;
            padding-right: 5rem; /* Space for the icon */
        }

        /* Custom Theme Overrides */
        :root {
            --bs-primary: #FAD52E !important;
            --bs-primary-rgb: 250, 213, 46 !important;
        }

        .bg-gradient-primary {
            background-image: linear-gradient(195deg, #FAD52E 0%, #d4b527 100%) !important;
        }

        .btn-primary, .btn.bg-gradient-primary {
            background-image: linear-gradient(195deg, #FAD52E 0%, #d4b527 100%) !important;
            border-color: #FAD52E !important;
            color: #000 !important; /* Yellow background usually needs dark text */
        }
        
        .btn-primary:hover, .btn.bg-gradient-primary:hover {
            background-color: #FAD52E !important;
            border-color: #FAD52E !important;
            transform: scale(1.02);
        }

        .shadow-primary {
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(250, 213, 46, 0.4) !important;
        }

        .text-primary {
            color: #d4b527 !important; /* Slightly darker yellow for text readability */
        }

        .nav-link.active .i {
            color: #FAD52E !important;
        }
    </style>
    @if (app()->getLocale() == 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    @endif
</head>
<body class="{{ $bodyClass }} {{ app()->getLocale() == 'ar' ? 'rtl' : '' }}">

{{ $slot }}
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery Validation -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap (bundle includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

<script src="{{ asset('dashboard/assets') }}/js/core/popper.min.js"></script>
<script src="{{ asset('dashboard/assets') }}/js/core/bootstrap.min.js"></script>
<script src="{{ asset('dashboard/assets') }}/js/plugins/perfect-scrollbar.min.js"></script>
<script src="{{ asset('dashboard/assets') }}/js/plugins/smooth-scrollbar.min.js"></script>
@stack('js')
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    var isRtl = "{{ app()->getLocale() }}" == 'ar';
    
    // Disable SmoothScrollbar in RTL as it doesn't support it well
    if (win && document.querySelector('#sidenav-scrollbar') && !isRtl) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{ asset('dashboard/assets') }}/js/material-dashboard.min.js?v=3.0.0"></script>
</body>
</html>
