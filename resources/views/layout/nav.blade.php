<style>
    .breadcrumb-wrapper {
        margin-right: 25%;
    }
</style>
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow "
    style="left: 0; max-width: 99%;">
    <div class="navbar-container w-100">
        {{-- @php dd(Session::all()); @endphp --}}
        <div class="row">
            <div class="col-sm-1">
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">

                            <!-- <img class="rounded" src={{ asset('app-assets/images/logo/cc_logo.jpg') }} alt="avatar"
                                width="auto" height="40"> -->
                                {{-- <a class="offcanvas__logo_link" href="index.php"> --}}
                            <img src={{ asset('assets/logo/ytcr-logo.png') }} style="height: 45px;" alt="YooTooCanRun Logo"/>
                            </a>
                            {{-- <img class="round" src="app-assets/images/logo/logo.png" alt="avatar" width="auto"
                                height="40"> --}}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-sm-10">
                <ul class="nav ms-auto text-center mx-2 d-flex justify-content-center" style="gap: 10px">
                    @php
                        $uriSegment = request()->segments(0);
                        $uriSegment = $uriSegment[0];
                        // dd($uriSegment);
                    @endphp

                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            if ($uriSegment == 'dashboard') {
                                echo 'active';
                            }
                            ?>" href="{{ url('/dashboard') }}"
                                type="button">

                                <i class="fa fa-dashboard" aria-hidden="true"></i>
                                Dashboard
                            </a>
                        </div>
                    </li>

                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            if ($uriSegment == 'users') {
                                echo 'active';
                            }
                            ?>" href="{{ url('/users') }}"
                                type="button">

                                <i class="fa fa-users" aria-hidden="true"></i>
                                User
                            </a>
                        </div>
                    </li> 
                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            // if ($uriSegment == 'category') {
                            //     echo 'active';
                            // }
                            ?>" href="{{ url('/category') }}"
                                type="button">

                                <i class="fa fa-users" aria-hidden="true"></i>
                                Category
                            </a>
                        </div>
                    </li>
                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            // if ($uriSegment == 'category') {
                            //     echo 'active';
                            // }
                            ?>" href="{{ url('/banner') }}"
                                type="button">

                                <i class="fa fa-users" aria-hidden="true"></i>
                                Banner
                            </a>
                        </div>
                    </li>
                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            // if ($uriSegment == 'category') {
                            //     echo 'active';
                            // }
                            ?>" href="{{ url('/advertisement') }}"
                                type="button">

                                <i class="fa fa-users" aria-hidden="true"></i>
                                Advertisement
                            </a>
                        </div>
                    </li>
                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            if ($uriSegment == 'events') {
                                echo 'active';
                            }
                            ?>" href="{{ url('/event') }}"
                                type="button">

                                <i class="fa fa-users" aria-hidden="true"></i>
                                Events

                            </a>
                        </div>
                    </li>
                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            if ($uriSegment == 'testimonial') {
                                echo 'active';
                            }
                            ?>" href="{{ url('/testimonial') }}"
                                type="button">

                                <i class="fa fa-users" aria-hidden="true"></i>
                                Testimonial

                            </a>
                        </div>
                    </li>

                    <li class="nav-item d-inline ">
                        <div class="btn-group">
                            <a class="btn btn-flat-primary <?php
                            if ($uriSegment == 'type') {
                                echo 'active';
                            }
                            ?>" href="{{ url('/type') }}"
                                type="button">

                                <i class="fa fa-users" aria-hidden="true"></i>
                                Types

                            </a>
                        </div>
                    </li>


            </div>

            <div class="col-sm-1">
                <ul class="nav navbar-nav ms-auto flex-row-reverse">
                    <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link"
                            id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder">
                                    @php
                                        $admin = Session::get('logged_in');
                                        //    $athlete=Session::get(' athelete_logged_in');
                                        if (!empty($admin)) {
                                            $username = Session::get('logged_in')['firstname'];
                                            echo $username;
                                        }


                                    @endphp
                                </span>
                                <span class="user-status bg-warning">
                                    <?php
                                //    session()->flush();
                                    $superuser = isset(Session::get('logged_in')['type']) ? Session::get('logged_in')['type'] : '';
                                    // dd($superuser);
                                    if (!empty($superuser) && ($superuser)==1) {
                                        echo 'Superadmin';
                                    }

                                    ?>
                                </span>

                                <?php //dd($superuser,$subadmin,$athlete);
                                ?>
                            </div>
                            <span class="avatar"><img class="round"
                                    src="{{ asset('app-assets/images/logo/logo.jpg')}}" alt="avatar" height="40"
                                    width="40"><span class="avatar-status-online"></span></span>
                        </a>
                        <!-- <span class="avatar"><img class="round"
                                    src="{{ asset('assets/logo/ytcr-logo.png')}}" alt="avatar" height="40"
                                    width="40"><span class="avatar-status-online"></span></span>
                        </a> -->

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user" id="myDIV">

                            <?php

                            $SuperAdmin=(!empty(Session::get('logged_in')['type'])) ? Session::get('logged_in')['type']: 0;

                           if(($SuperAdmin)==1)
                           {?>
                            <a class="dropdown-item" href="#"><i class="me-50"
                                    data-feather="user"></i>Profile</a>

                            <a class="dropdown-item" href="#"><i class="me-50" data-feather="settings"></i>
                                Settings</a>

                            <a class="dropdown-item" href="{{ url('backup-download') }}"><i class="me-50" data-feather="download"></i>
                                Database Backup</a>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('/logout') }}"><i class="me-50"
                                    data-feather="power"></i> Logout</a>
                            <?php }?>
                        </div>
                    </li>
                </ul>

                {{-- <button type="button" class="btn btn-primary waves-effect waves-float waves-light" onclick="toggleNav(); getEvent();" style="position: fixed; margin: 39px 0 0 50px; z-index: 999999;"> Events</button> --}}

            </div>
            <script>

                $(".dropdown-user").click(function() {
                    var element = document.getElementById("myDIV");
                    element.classList.toggle("show");
                });


            </script>

</nav>
