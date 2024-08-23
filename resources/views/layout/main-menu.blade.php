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
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M680-80q-83 0-141.5-58.5T480-280q0-83 58.5-141.5T680-480q83 0 141.5 58.5T880-280q0 83-58.5 141.5T680-80Zm-200 0q-139-35-229.5-159.5T160-516v-244l320-120 320 120v227q-26-13-58.5-20t-61.5-7q-116 0-198 82t-82 198q0 62 23.5 112T483-81q-1 0-1.5.5t-1.5.5Zm200-200q25 0 42.5-17.5T740-340q0-25-17.5-42.5T680-400q-25 0-42.5 17.5T620-340q0 25 17.5 42.5T680-280Zm0 120q31 0 57-14.5t42-38.5q-22-13-47-20t-52-7q-27 0-52 7t-47 20q16 24 42 38.5t57 14.5Z"/></svg>
            {{-- <i data-feather='user'></i> --}}
            <span class="menu-title text-truncate" data-i18n="Invoice">{{ $user['firstname'] }} {{ $user['lastname'] }}</span>
            </a>
            <ul class="menu-content">
               <li>
                  <a class="d-flex align-items-center" href="{{ url('/user_profile') }}">
                     <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
                  <span class="menu-item text-truncate" data-i18n="List">Profile</span>
                  </a>
               </li>
       
               <li>
                  <a class="d-flex align-items-center" href="{{ url('/logout') }}">
                     <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                  {{-- <i data-feather='log-out'></i> --}}
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
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M520-600v-240h320v240H520ZM120-440v-400h320v400H120Zm400 320v-400h320v400H520Zm-400 0v-240h320v240H120Zm80-400h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z"/></svg>
               {{-- <i class="fa fa-dashboard" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate"> Dashboard</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'users') echo 'active'; ?>">
            <a class="d-flex align-items-center" href="{{ url('users') }}" >
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm720 0v-120q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v120H760ZM360-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm400-160q0 66-47 113t-113 47q-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate">User</span></a>
            </a>
         </li>
         <li class="nav-item  <?php if($uriSegment == 'category') echo 'active'; ?>">
            <a class="d-flex align-items-center" href="{{ url('/category') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate">Category Type</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'banner') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/banner') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M680-160v-640q33 0 56.5 23.5T760-720v480q0 33-23.5 56.5T680-160ZM160-80q-33 0-56.5-23.5T80-160v-640q0-33 23.5-56.5T160-880h360q33 0 56.5 23.5T600-800v640q0 33-23.5 56.5T520-80H160Zm680-160v-480q25 0 42.5 17.5T900-660v360q0 25-17.5 42.5T840-240Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate"> Banner</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'advertisement') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/advertisement') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M120-200q-33 0-56.5-23.5T40-280v-400q0-33 23.5-56.5T120-760h400q33 0 56.5 23.5T600-680v400q0 33-23.5 56.5T520-200H120Zm40-160h320L376-500l-76 100-56-74-84 114Zm520 160v-560h80v560h-80Zm160 0v-560h80v560h-80Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate">  Advertisement</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'event') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/event') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M580-240q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate">   Event</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'testimonial') echo 'active'; ?>">
            <a class="d-flex align-items-center "  href="{{ url('/testimonial') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M240-400h480v-80H240v80Zm0-120h480v-80H240v80Zm0-120h480v-80H240v80Zm-80 400q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v720L720-240H160Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate"> Testimonial</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'type') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/type') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M360-720h80v-80h-80v80Zm160 0v-80h80v80h-80ZM360-400v-80h80v80h-80Zm320-160v-80h80v80h-80Zm0 160v-80h80v80h-80Zm-160 0v-80h80v80h-80Zm160-320v-80h80v80h-80Zm-240 80v-80h80v80h-80ZM200-160v-640h80v80h80v80h-80v80h80v80h-80v320h-80Zm400-320v-80h80v80h-80Zm-160 0v-80h80v80h-80Zm-80-80v-80h80v80h-80Zm160 0v-80h80v80h-80Zm80-80v-80h80v80h-80Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate">Races Category</span></a>
            </a>
         </li>
         <li class="nav-item <?php if($uriSegment == 'remittance_management') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/remittance_management ') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M531-260h96v-3L462-438l1-3h10q54 0 89.5-33t43.5-77h40v-47h-41q-3-15-10.5-28.5T576-653h70v-47H314v57h156q26 0 42.5 13t22.5 32H314v47h222q-6 20-23 34.5T467-502H367v64l164 178ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate">Remittance management </span></a>
            </a>
         </li>

         <li class="nav-item <?php if($uriSegment == 'email_sending') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/email_sending ') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280 320-200v-80L480-520 160-720v80l320 200Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate"> Email </span></a>
            </a>
         </li>

         <li class="nav-item <?php if($uriSegment == 'marketing') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/marketing ') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m136-240-56-56 296-298 160 160 208-206H640v-80h240v240h-80v-104L536-320 376-480 136-240Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate"> Marketing </span></a>
            </a>
         </li>

         <li class="nav-item <?php if($uriSegment == 'role_master') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/role_master ') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m640-120-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-8 21.5-13.5T628-460l12-60h80l12 60q12 5 22.5 11t21.5 15l58-20 40 70-46 40q2 12 2 25t-2 25l46 40-40 68-58-18q-11 8-21.5 13.5T732-180l-12 60h-80ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-29 72-24 143t48 135H80Zm600-80q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240ZM400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Z"/></svg>
               <span class="menu-title text-truncate"> Role Master </span></a>
            </a>
         </li>

         <li class="nav-item <?php if($uriSegment == 'organiser_master') echo 'active'; ?>">
            <a class="d-flex align-items-center " href="{{ url('/organiser_master ') }}">
               <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m159-168-34-14q-31-13-41.5-45t3.5-63l72-156v278Zm160 88q-33 0-56.5-23.5T239-160v-240l106 294q3 7 6 13.5t8 12.5h-40Zm206-4q-32 12-62-3t-42-47L243-622q-12-32 2-62.5t46-41.5l302-110q32-12 62 3t42 47l178 488q12 32-2 62.5T827-194L525-84Zm-86-476q17 0 28.5-11.5T479-600q0-17-11.5-28.5T439-640q-17 0-28.5 11.5T399-600q0 17 11.5 28.5T439-560Z"/></svg>
               {{-- <i class="fa fa-users" aria-hidden="true"></i> --}}
               <span class="menu-title text-truncate"> Organiser Master </span></a>
            </a>
         </li>


     </ul>
     
   </div>
</div>
<!-- END: Main Menu-->

