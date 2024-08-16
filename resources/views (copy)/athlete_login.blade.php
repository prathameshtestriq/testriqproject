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
    <title>Login - YouTooCanRun </title>
    <link rel="apple-touch-icon" href={{ asset('app-assets/images/ico/apple-icon-120.png')}}>
    <link rel="shortcut icon" type="image/x-icon" href={{ asset('assets/img/ico/favicon.ico')}}>
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
                <div class="auth-wrapper auth-v2">
                    <div class="auth-inner row m-0">
                        <!-- Brand logo-->
                        <a class="brand-logo" href="javascript:void(0);">
                            <img src={{ asset('app-assets/images/logo/logo.png') }} alt="">
                            {{-- <h2 class="brand-text text-primary ml-1">{{ env('APP_NAME') }}</h2> --}}
                        </a>
                        <!-- /Brand logo-->
                        <!-- Left Text-->
                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img
                                    class="img-fluid" src={{ asset('app-assets/images/pages/login-v2.svg') }}
                                    alt="Login V2" /></div>
                        </div>
                        <!-- /Left Text-->
                        <!-- Login-->
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <h2 class="card-title font-weight-bold mb-1">Welcome to Athlete! 👋</h2>
                                <p class="card-text mb-2">Please sign-in to your account</p>
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

                                <form class="auth-login-form mt-2" action="{{ url('athlete_login') }}" method="post">
                                    {{ @csrf_field() }}
                                    <div class="form-group">
                                        <label class="form-label" for="login-username">Username<span
                                                style="color:red;">*</span></label>
                                        <input class="form-control" id="login-username" type="text" name="username"
                                            placeholder="Enter Username" aria-describedby="login-username" autofocus=""
                                            tabindex="1" value="{{ old('username') }}" autocomplete="off"/>
                                        <span style="color:red;">
                                            <p>
                                                @error('username')
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label for="login-password">Password <span
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
                                    <button class="btn btn-primary btn-block" tabindex="4">Log in</button>
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

