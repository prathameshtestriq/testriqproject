<!DOCTYPE html>
@php $user=Session::get('logged_in'); 
    $data  = array();
    $data = DB::table('users')->where('id', $user['id'])->get();
    foreach ($data as $key => $value) {
    @endphp
        <html class="loading @php if ($value->theme_mode== 1) {
        echo 'dark-layout';
        } @endphp" lang="en" data-textdirection="ltr">
    <!-- BEGIN: Head-->
    @php
      }
    @endphp

@include('layout.header')


<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">

    <!-- BEGIN: Header-->
    @include('layout.nav')

    <!-- END: Header-->

    <!-- BEGIN: Side Menu -->
    @include('layout.main-menu')

    <!-- END: Side Menu -->

    <!-- BEGIN: Content-->
    @include('layout.user')

    
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>

    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('layout.footer')
    

    <script src={{ asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}></script>
    
    <!-- END: Footer-->

    <!-- BEGIN: Page Vendor JS-->
    {{-- <script src={{ asset('app-assets/vendors/js/charts/apexcharts.min.js')}}></script> --}}
    
    {{-- <script src={{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}></script> --}}
    <!-- END: Page Vendor JS-->


    <!-- BEGIN: Page Vendor JS-->
    <script src={{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}></script>

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
    <script src={{ asset('app-assets/js/scripts/forms/pickers/form-pickers.js')}}></script>
    <!-- END: Page JS-->

    <!-- BEGIN: Page JS-->
    {{-- <script src={{ asset('app-assets/js/scripts/pages/dashboard-ecommerce.js')}}></script> --}}
    <!-- END: Page JS-->
    

    
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