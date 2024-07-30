<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
   <div class="navbar-header">
      <ul class="nav navbar-nav flex-row">
         <li class="nav-item mr-auto">
           
            <?php if(Session::get('logged_in')['type'] == 1 || Session::get('logged_in')['user_login'] == 1){ ?>
            <a class="navbar-brand" href="{{ url('/admin_dashboard') }}">
               <span class="brand-logo">
               <img src={{ asset('app-assets/images/logo/logo.png') }} alt="">
               </span>
               <h2 class="brand-text">R A C E S</h2>
            </a>
            <?php }else{ ?>
                <a class="navbar-brand" href="{{ url('/user_dashboard/0') }}">
               <span class="brand-logo">
               <img src={{ asset('app-assets/images/logo/logo.png') }} alt="">
               </span>
               <h2 class="brand-text">R A C E S</h2>
            </a>
            <?php } ?>
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
                  <a class="d-flex align-items-center" href="{{ url('/admin') }}">
                  <i data-feather='log-out'></i>
                  <span class="menu-item text-truncate" data-i18n="Edit">Logout</span>
                  </a>
               </li>
               
            </ul>
         </li>
         
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
{{--         
         @if(Session::get('logged_in')['superuser'])
             <li class="nav-item <?php if($uriSegment == 'modules') echo 'active'; ?>"><a class="d-flex align-items-center" href="{{ url('modules') }}">
               <i class="fa fa-address-book" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Modules</span></a>
             </li>      
         @endif          --}}

         
         <li class="nav-item <?php if($uriSegment == 'dashboard') echo 'active'; ?>">
            <a class="d-flex align-items-center" href="{{ url('dashboard') }}">
               <i class="fa fa-dashboard" aria-hidden="true"></i>
               <span class="menu-title text-truncate"> Dashboard</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'users') echo 'active'; ?>">
            <a class="d-flex align-items-center" href="{{ url('users') }}" >
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate">User</span></a>
            </a>
         </li>
         <li class="nav-item  <?php if($uriSegment == 'category') echo 'active'; ?>">
            <a class="d-flex align-items-center" href="{{ url('/category') }}">
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Category Type</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'banner') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/banner') }}">
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate"> Banner</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'advertisement') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/advertisement') }}">
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate">  Advertisement</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'event') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/event') }}">
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate">   Events</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'testimonial') echo 'active'; ?>">
            <a class="d-flex align-items-center "  href="{{ url('/testimonial') }}">
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate"> Testimonial</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'type') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/type') }}">
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Rest Category</span></a>
            </a>
         </li>
         <li class="nav-item">
            <a class="d-flex align-items-center " href="{{ url('/remittance_management ') }}">
               <i class="fa fa-users" aria-hidden="true"></i>
               <span class="menu-title text-truncate">Remittance management </span></a>
            </a>
         </li>
     

     </ul>
     
   </div>
</div>
<!-- END: Main Menu-->

