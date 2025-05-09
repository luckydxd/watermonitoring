 <!-- Menu -->

 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand demo">
         <a href="{{ route('dashboard') }}" class="app-brand-link">
             <span class="app-brand-logo demo menu-text fw-bold" style="min-width: 138px;">
                 Water Monitoring
             </span>
             {{-- <span class="app-brand-text demo menu-text fw-bold" style="font-size: 14px;">Monitoring</span>
    </a> --}}

             <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                 <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
                 <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
             </a>
     </div>

     <div class="menu-inner-shadow"></div>

     <ul class="menu-inner py-1">
         <!-- Page -->
         <li class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
             <a href="{{ route('dashboard') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-smart-home"></i>
                 <div data-i18n="Page 1">Dashboard</div>
             </a>
         </li>

         <li class="menu-item {{ Request::is('user') ? 'active' : '' }}">
             <a href="{{ route('user') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-users"></i>
                 <div data-i18n="Page 2">Manage Users</div>
             </a>
         </li>

         <li class="menu-item {{ Request::is('device') ? 'active' : '' }}">
             <a href="{{ route('device') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-cpu"></i>
                 <div data-i18n="Page 3">Manage IoT Devices</div>
             </a>
         </li>

         <li class="menu-item {{ Request::is('monitor') ? 'active' : '' }}">
             <a href="{{ route('monitor') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-device-desktop-analytics"></i>
                 <div data-i18n="Page 3">Monitor Water Usage</div>
             </a>
         </li>

         <li class="menu-item">
             <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                 <div data-i18n="Website Settings">Website Settings</div>
             </a>
             <ul class="menu-sub">
                 <li class="menu-item">
                     <a href="{{ route('admin.landing.hero') }}" class="menu-link">
                         <div data-i18n="Hero Section">Hero Section</div>
                     </a>
                 </li>
                 <li class="menu-item">
                     <a href="{{ route('admin.landing.about') }}" class="menu-link">
                         <div data-i18n="About">Tentang Kami</div>
                     </a>
                 </li>
                 <li class="menu-item">
                     <a href="{{ route('admin.landing.features') }}" class="menu-link">
                         <div data-i18n="Features">Fitur</div>
                     </a>
                 </li>
                 <li class="menu-item">
                     <a href="{{ route('admin.landing.contact') }}" class="menu-link">
                         <div data-i18n="Contact">Kontak</div>
                     </a>
                 </li>
                 <li class="menu-item">
                     <a href="{{ route('admin.landing.footer') }}" class="menu-link">
                         <div data-i18n="Footer">Footer & Sosial Media</div>
                     </a>
                 </li>
             </ul>
         </li>

     </ul>
 </aside>
 <!-- / Menu -->
