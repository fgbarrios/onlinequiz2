<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{asset('admin/assets/')}}" data-template="vertical-menu-template-free">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
      <title>{{env('APP_NAME')}}</title>
      <meta name="description" content="" />
      <META NAME="robots" CONTENT="noindex,nofollow">
      <!-- Favicon -->
      <link rel="icon" type="image/x-icon" href="{{asset('admin/assets/img/favicon/favicon.ico')}}" />
      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
      <link href="https://fonts.googleapis.com/css2?family=Akshar:wght@300;400;500;600;700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
      <!-- Icons. Uncomment required icon fonts -->
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/fonts/boxicons.css')}}" />
      <!-- Core CSS -->
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
      <link rel="stylesheet" href="{{asset('admin/assets/css/demo.css')}}" />
      <!-- Vendors CSS -->
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/apex-charts/apex-charts.css')}}" />
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
      <style type="text/css">
         .bg-menu-theme {
         background-color: #211c4e !important;
         color: #697a8d !important;
         }
         .bg-menu-theme .menu-link, .bg-menu-theme .menu-horizontal-prev, .bg-menu-theme .menu-horizontal-next 
         {
         color: #ffffff;
         }
         .bg-menu-theme .menu-link:hover, .bg-menu-theme .menu-horizontal-prev:hover, .bg-menu-theme .menu-horizontal-next:hover 
         {
         color: #aba3a3;
         }
         .bg-menu-theme .menu-inner > .menu-item.active > .menu-link {
         color: #ffffff !important;
         background-color: rgb(255 255 255 / 16%) !important;
         }
         .invalid-data
         {
            color: #ff5151;
            font-size: 13px;
            margin: 5px;
            font-weight: 500;
         }
      </style>
      <!-- Page CSS -->
      <!-- Helpers -->
      <script src="{{asset('admin/assets/vendor/js/helpers.js')}}"></script>
      <script src="{{asset('admin/assets/js/config.js')}}"></script>
   </head>
   <body>

      <!-- Layout wrapper -->
      <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
               <div class="app-brand demo">
                  <a href="{{url('/')}}" class="app-brand-link">
                  <img src="{{asset('admin/assets/logo.svg')}}" alt="Logo" style="width: 80px;margin-left: 3rem;">
                  </a>
                  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                  <i class="bx bx-chevron-left bx-sm align-middle"></i>
                  </a>
               </div>
               <div class="menu-inner-shadow"></div>
               <ul class="menu-inner py-1">
                  <!-- Dashboard -->
                  <li class="menu-item <?=(Route::currentRouteName())==="dashboard"?'active':''; ?>">
                     <a href="{{url('/dashboard')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div data-i18n="Analytics">{{__('admin.dashboard')}}</div>
                     </a>
                  </li>
                  <!-- Settings -->
                  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{__('admin.settings')}}</span></li>
                  <!-- Activity Settings -->
                  <li class="menu-item <?=(Route::currentRouteName())==="activity-settings"?'active':''; ?>">
                     <a href="{{route('activity-settings')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.activity_settings')}}</div>
                     </a>
                  </li>
                  <!-- General Settings -->
                  <li class="menu-item <?=(Route::currentRouteName())==="general-settings"?'active':''; ?>">
                     <a href="{{route('general-settings')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.general_settings')}}</div>
                     </a>
                  </li>
                  <!-- Activities -->
                  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{__('admin.activities')}}</span></li>
                  <!-- Go to Activity -->
                  <li class="menu-item <?=(Route::currentRouteName())==="list-activity"?'active':''; ?>">
                     <a href="{{url('/list-activity')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.go_to_activity')}}</div>
                     </a>
                  </li>
                  <!-- Add New Activity -->
                  <li class="menu-item <?=(Route::currentRouteName())==="add-activity"?'active':''; ?>">
                     <a href="{{url('/add-activity')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.add_new')}} {{__('admin.activity')}}</div>
                     </a>
                  </li>
                  <!-- Trash Activity -->
                  <li class="menu-item <?=(Route::currentRouteName())==="list-trash"?'active':''; ?>">
                     <a href="{{url('/list-trash')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.trash')}}</div>
                     </a>
                  </li>
                  <!-- Account -->
                  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{__('admin.account')}}</span></li>
                  <!-- Profile -->
                  <li class="menu-item <?=(Route::currentRouteName())==="profile"?'active':''; ?>">
                     <a href="{{url('/profile')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.profile')}}</div>
                     </a>
                  </li>
                  <!-- Change Password -->
                  <li class="menu-item <?=(Route::currentRouteName())==="change-password"?'active':''; ?>">
                     <a href="{{url('/change-password')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.change_password')}}</div>
                     </a>
                  </li>
                  <!-- Logout -->
                  <li class="menu-item ">
                     <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">{{__('admin.logout')}}</div>
                     </a>
                  </li>
                  <!-- Change Language -->
                  <!-- <li class="menu-item">
                     <a href="{{url('/change-language/en')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">English</div>
                     </a>
                  </li>
                  <li class="menu-item">
                     <a href="{{url('/change-language/ta')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-collection"></i>
                        <div data-i18n="Basic">தமிழ்</div>
                     </a>
                  </li> -->
               </ul>
            </aside>
            <!-- / Menu -->
            <!-- Layout container -->
            <div class="layout-page">
               <!-- Navbar -->
               <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar" >
                  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                     <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                     <i class="bx bx-menu bx-sm"></i>
                     </a>
                  </div>
                  @php

                     $navbar_name1 = "";
                     $navbar_name2 = "";
                     $parames     = Request::segments()[0];
                     
                     if($parames=='dashboard')
                     {
                        $navbar_name1 = "";
                        $navbar_name2 = trans('admin.dasboard');
                     }
                     else if($parames=='activity-settings')
                     {
                        $navbar_name1 = trans('admin.settings');
                        $navbar_name2 = trans('admin.activity_settings');
                     }
                     else if($parames=='general-settings')
                     {
                        $navbar_name1 = trans('admin.settings');
                        $navbar_name2 = trans('admin.general_settings');
                     }
                     else if($parames=='list-activity')
                     {
                        $navbar_name1 = trans('admin.activity');
                        $navbar_name2 = trans('admin.go_to_activity');
                     }
                     else if($parames=='add-activity' || $parames=='edit-activity')
                     {
                        $navbar_name1 = trans('admin.activity');
                        if($parames=='add-activity')
                        {
                           $navbar_name2 = trans('admin.add_new');
                        }
                        else
                        {
                           $navbar_name2 = trans('admin.update');
                        }
                     }
                     else if($parames=='list-trash')
                     {
                        $navbar_name1 = trans('admin.activity');
                        $navbar_name2 = trans('admin.trash');
                     }
                     else if($parames=='profile')
                     {
                        $navbar_name1 = trans('admin.account');
                        $navbar_name2 = trans('admin.profile');
                     }
                     else if($parames=='change-password')
                     {
                        $navbar_name1 = trans('admin.account');
                        $navbar_name2 = trans('admin.change_password');
                     }
                     else if($parames=='see-response')
                     {
                        $navbar_name1 = trans('admin.activity');
                        $navbar_name2 = trans('admin.response_history');
                     }
                     else if($parames=='see-graph-response')
                     {
                        $navbar_name1 = trans('admin.activity');
                        $navbar_name2 = trans('admin.response_result');
                     }
                  @endphp
                  <div class="navbar-nav-right d-flex align-items-center justify-content-between" id="navbar-collapse">
                     <h4 class="fw-bold" style="margin-top: 1rem;">@if($navbar_name1 !="")<span class="text-muted"> {{$navbar_name1}} / </span>@endif {{$navbar_name2}}</h4>
                  </div>
               </nav> 
               <!-- / Navbar -->
               <!-- Content wrapper -->
               <div class="content-wrapper ">
                  @yield('content')
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                     @csrf
                  </form>
                  <!-- / Content -->
                  
                  <div class="content-backdrop fade"></div>
               </div>
               <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
         </div>
         <!-- Overlay -->
         <div class="layout-overlay layout-menu-toggle"></div>
      </div>
      <!-- / Layout wrapper -->
      <!-- Core JS -->
      <!-- build:js assets/vendor/js/core.js -->
      <script src="{{asset('admin/assets/vendor/libs/jquery/jquery.js')}}"></script>
      <script src="{{asset('admin/assets/vendor/libs/popper/popper.js')}}"></script>
      <script src="{{asset('admin/assets/vendor/js/bootstrap.js')}}"></script>
      <script src="{{asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
      <script src="{{asset('admin/assets/vendor/js/menu.js')}}"></script>
      <!-- endbuild -->
      <!-- Vendors JS -->
      <script src="{{asset('admin/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
      <!-- Main JS -->
      <script src="{{asset('admin/assets/js/main.js')}}"></script>
      <!-- Page JS -->
      <script src="{{asset('admin/assets/js/dashboards-analytics.js')}}"></script>
      <!-- Place this tag in your head or just before your close body tag. -->
      <script async defer src="https://buttons.github.io/buttons.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
      @stack('scripts')
      <script type="text/javascript">
         $(".select2").select2();
         setTimeout(function(){
            $(".alert").hide();
         },4000);
      </script>
   </body>
</html>