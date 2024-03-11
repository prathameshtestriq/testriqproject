<!-- BEGIN: Main Menu-->
{{-- @php $aModules = Session::get('modules'); dd($aModules); @endphp --}}
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
   <div class="navbar-header">
      <ul class="nav navbar-nav flex-row">
         <li class="nav-item mr-auto">
            <a class="navbar-brand" href="{{ url('/dashboard') }}">
               <span class="brand-logo">
               <img src={{ asset('app-assets/images/logo/logo.png') }} alt="">
               </span>
               {{-- <h2 class="brand-text">{{ env('APP_NAME') }}</h2> --}}
            </a>
         </li>
         <li class="nav-item nav-toggle">
            <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
            <i class="d-block d-xl-none text-white toggle-icon font-medium-4" data-feather="x"></i>

            <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i>

            </a>
         </li>
      </ul>
   </div>
   <div class="shadow-bottom"></div>
   <div class="main-menu-content">
      
      <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
         @php $uriSegment = request()->segments(0);
         $uriSegment = $uriSegment[0]; 
         @endphp

         <li class=" nav-item">
            @php $user=Session::get('logged_in');  @endphp
            <a class="d-flex align-items-center" href="#">
            <i data-feather='user'></i>
            <span class="menu-title text-truncate" data-i18n="Invoice">{{ $user['firstname'] }} {{ $user['lastname'] }}</span>
            </a>
            <ul class="menu-content">
               <li>
                  <a class="d-flex align-items-center" href="{{ url('/user_profile') }}">
                  <i data-feather='user'></i>
                  <span class="menu-item text-truncate" data-i18n="List">Profile</span>
                  </a>
               </li>
       
               <li>
                  <a class="d-flex align-items-center" href="{{ url('logout') }}">
                  <i data-feather='log-out'></i>
                  <span class="menu-item text-truncate" data-i18n="Edit">Logout</span>
                  </a>
               </li>
               
            </ul>
         </li>

          <!-- admin dashboard-->
         <?php //if(Session::get('logged_in')['superuser'] == 1 || Session::get('logged_in')['subadmin'] == 1){ ?> 
            {{-- <li class="nav-item <?php //if($uriSegment == 'admin_dashboard') echo 'active'; ?>"><a class="d-flex align-items-center" href="{{ url('/admin_dashboard') }}">
               <i class="fa fa-dashboard" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Dashboard</span></a>
            </li> --}}

            <li class="nav-item <?php if($uriSegment == 'dashboard') echo 'active'; ?>"><a class="d-flex align-items-center" href="{{ url('/dashboard') }}">
               <i class="fa fa-dashboard" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Dashboard</span></a>
            </li>
         <?php //} ?>
         <?php //}else{ ?>  <!-- User dashboard-->
           <!--  <li class="nav-item <?php //if($uriSegment == 'user_dashboard') echo 'active'; ?>"><a class="d-flex align-items-center" href="{{ url('/user_dashboard') }}">
               <i class="fa fa-dashboard" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Dashboard</span></a> -->
            <!-- </li> -->
         <?php //} ?>

         {{-- <li class="nav-item <?php //if($uriSegment == 'home') echo 'active'; ?>"><a class="d-flex align-items-center" href="{{ url('/home') }}">
            <i class="fa fa-globe" aria-hidden="true"></i>
            <span class="menu-title text-truncate">Real Time Map</span></a>
         </li> --}}
         
         @if(Session::has('modules'))
             @php $aModules = Session::get('modules'); @endphp

             @foreach($aModules as $key=>$val)
                 <li class="nav-item <?php if($uriSegment == $val['module_link']) echo 'active'; ?>" ><a class="d-flex align-items-center" href="{{ url($val['module_link']) }}">
                 <?php if(!empty($val['module_fa_icon'])){ ?>
                    <i class="{{$val['module_fa_icon']}}" aria-hidden="true"></i>
                  <?php }else{ ?> 
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                  <?php } ?>
                  <span class="menu-title text-truncate">{{$key}}</span></a>
                 </li> 
             @endforeach
         @endif
        
         @if(Session::get('logged_in')['superuser'])
             <li class="nav-item <?php if($uriSegment == 'modules') echo 'active'; ?>"><a class="d-flex align-items-center" href="{{ url('modules') }}">
               <i class="fa fa-address-book" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Modules</span></a>
             </li>      
         @endif   


     </ul>
     
   </div>
</div>
<!-- END: Main Menu-->

