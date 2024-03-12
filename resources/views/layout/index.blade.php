<!DOCTYPE html>


<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="vts, nashik ">
    <meta name="keywords" content="vts, nashik, nmc">
    <meta name="author" content="swt">
    <title><?php echo config('custom.page_title'); ?>@yield('title')</title>
    {{-- <link rel="apple-touch-icon" href={{ asset('app-assets/images/ico/apple-icon-120.png')}}> --}}
    <link rel="shortcut icon" type="image/x-icon" href={{ asset('app-assets/images/logo/logo.jpg')}}>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/vendors.min.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/forms/wizard/bs-stepper.min.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/charts/apexcharts.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/extensions/toastr.min.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}>
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/bootstrap.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/bootstrap-extended.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/colors.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/components.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/themes/dark-layout.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/themes/bordered-layout.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/themes/semi-dark-layout.css')}}>

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/plugins/forms/form-validation.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/plugins/forms/form-wizard.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/pages/dashboard-ecommerce.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/plugins/charts/chart-apex.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/plugins/extensions/ext-component-toastr.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/plugins/forms/pickers/form-pickadate.css')}}>
     <!--Sortable -->
    <!-- END: Page CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('assets/css/style.css')}}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/custom.css')}}>
    <!-- END: Custom CSS-->
    {{-- <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script> --}}
    {{-- <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script> --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- JQUERY-->
    {{-- <script src={{ asset('assets/js/jquery3.6.4.min.js')}}></script> --}}
</head>


<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">

    <!-- BEGIN: Header-->
    @include('layout.nav')

    <!-- END: Header-->

    <!-- BEGIN: Side Menu
    {{-- @include('layout.main-menu') --}} -->

    <!-- END: Side Menu -->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">

                <!-- Table Hover Animation start -->
                @section('content')

                @show
                <!-- Table head options end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>

    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
    <div class="footerContainer">
        <!-- <p class="copyright text-center">© Cotton Connect MEL 2022</p> -->
        <p class="copyright text-center"></p>
    </div>
    </footer>

    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src={{ asset('app-assets/vendors/js/vendors.min.js')}}></script>
    <!-- BEGIN Vendor JS-->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <!-- BEGIN: Page Vendor JS-->
    <script src={{ asset('app-assets/vendors/js/forms/wizard/bs-stepper.min.js')}}></script>
    <script src={{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    {{-- <script src={{ asset('app-assets/vendors/js/charts/apexcharts.min.js')}}></script> --}}
    {{-- <script src={{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}></script> --}}
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src={{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}></script>
    <script src={{ asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}></script>

    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src={{ asset('app-assets/js/core/app-menu.js')}}></script>
    <script src={{ asset('app-assets/js/core/app.js')}}></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src={{ asset('app-assets/js/scripts/forms/form-number-input.js')}}></script>
    <!-- END: Page JS-->

    <!-- BEGIN: Page JS-->
    <script src={{ asset('app-assets/js/scripts/forms/form-select2.js')}}></script>
    <!-- END: Page JS-->

    <!-- <script src={{ asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}></script> -->
     <!-- BEGIN: Page JS-->
    {{-- <script src={{ asset('app-assets/js/scripts/forms/pickers/form-pickers.js')}}></script> --}}
    <!-- END: Page JS-->

    <!-- BEGIN: Page JS-->
    {{-- <script src={{ asset('app-assets/js/scripts/pages/dashboard-ecommerce.js')}}></script> --}}
    <!-- END: Page JS-->

    <script src={{ asset('app-assets/js/scripts/forms/form-wizard.js')}}></script>
    {{-- <script src={{ asset('app-assets/js/scripts/components/components-accordion.js')}}></script> --}}

    <script src="https://malsup.github.io/jquery.form.js"></script>
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })

      var base_url = "<?php echo config('custom.base_url'); ?>";
    </script>
</body>
<!-- END: Body-->

</html>
