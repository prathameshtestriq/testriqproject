<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="vts">
    <meta name="keywords" content="vts, nashik">
    <meta name="author" content="swt">
    <title>Login -CC-MEL </title>
    {{-- <link rel="apple-touch-icon" href={{ asset('app-assets/images/ico/apple-icon-120.png')}}> --}}
    <link rel="shortcut icon" type="image/x-icon" href={{ asset('app-assets/images/logo/logo.jpg')}}>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
        rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/vendors/css/vendors.min.css') }}>
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/bootstrap.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/bootstrap-extended.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/colors.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/components.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/themes/dark-layout.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/themes/bordered-layout.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/themes/semi-dark-layout.css') }}>

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/plugins/forms/form-validation.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('app-assets/css/pages/page-auth.css') }}>
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href={{ asset('assets/css/style.css') }}>
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<?php //\Session::flush();?>
<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static   menu-collapsed"
    data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="wrapper ">
                    <div class=" row m-0">
                        <!-- Brand logo-->
                        <a class="brand-logo" href="javascript:void(0);">
                            <img src={{ asset('app-assets/images/logo/bg-top.png') }} alt="" width="1550px" >
                            {{-- <h2 class="brand-text text-primary ml-1">{{ env('APP_NAME') }}</h2> --}}
                        </a>
                        <!-- /Brand logo-->
                        <!-- Left Text-->
                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5  auth-bg" >
                            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img class="img-fluid" src={{ asset('app-assets/images/logo/cc_logo.jpg') }} alt="Login V2"  width="500px"/></div>
                        </div>
                        <!-- /Left Text-->
                        <!-- Login-->
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5" style="color:rgb(181, 208, 231)">
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <h2 class="card-title font-weight-bold mb-1 " style="color:#202B63">Welcome to Cotton Connect! 👋</h2>
                                <p class="card-text mb-2" style="color:#2a335f">Please sign-in to your account</p>
                                @if ($message = Session::get('success'))
                                    <div class="demo-spacing-0 mb-1">
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <div class="alert-body">
                                                <i class="fa fa-check-circle" style="font-size:16px;"
                                                    aria-hidden="true"></i>
                                                {{ $message }}
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">

                                            </div>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                @elseif ($message = Session::get('error'))
                                    <div class="demo-spacing-0 mb-1">
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <div class="alert-body">
                                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                                {{ $message }}
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">

                                            </div>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <form class="auth-login-form mt-2" action="{{ url('login') }}" method="post">
                                    {{ @csrf_field() }}
                                    <div class="form-group">
                                        <label class="form-label" for="login-email" style="color:#202B63">Email<span
                                                style="color:red;">*</span></label>
                                        <input class="form-control" id="login-email" type="text" name="email"
                                            placeholder="Enter Email" aria-describedby="login-email" autofocus=""
                                            tabindex="1" value="{{ old('email') }}" autocomplete="off"/>
                                        <span style="color:red;">
                                            <p>
                                                @error('email')
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label for="login-password" style="color:#202B63">Password <span
                                                    style="color:red;">*</span></label>
                                            <!-- <a href="page-auth-forgot-password-v2.html"><small>Forgot Password?</small></a> -->
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input class="form-control form-control-merge" id="login-password"
                                                type="password" name="password" placeholder="Enter Password"
                                                aria-describedby="login-password" tabindex="2" />
                                            <div class="input-group-append"><span
                                                    class="input-group-text cursor-pointer"><i
                                                        data-feather="eye"></i></span></div>
                                        </div>
                                        <p style="color:red;">
                                            @error('password')
                                                {{ $message }}
                                            @enderror
                                        </p>
                                    </div>
                                    <input type="hidden" name="command" value="login" />
                                    <button class="btn btn-primary btn-block" style="background-color:#202B63 !important" tabindex="4">Sign in</button>
                                </form>


                            </div>
                        </div>
                        <!-- /Login-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

