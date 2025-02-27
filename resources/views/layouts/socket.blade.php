<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{asset('admin/assets/')}}" data-template="vertical-menu-template-free">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
      <title>{{env('APP_NAME')}}</title>
      <meta name="description" content="" />
      <META NAME="robots" CONTENT="noindex,nofollow">
      <link rel="icon" type="image/x-icon" href="{{asset('admin/assets/img/favicon/favicon.ico')}}" />
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/fonts/boxicons.css')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
      <style>
         body{
            font-family: "inter";
         }
      </style>

      <script src="{{asset('admin/assets/vendor/js/helpers.js')}}"></script>
      <script src="{{asset('admin/assets/js/config.js')}}"></script>
   </head>
   <body>
      <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
            <div class="layout-page">
               <div class="content-wrapper">
                  <div class="container-xxl flex-grow-1 container-p-y">
                     <div class="container-fluid vh-100" style="margin-top:10px" id="socket_response">
                       
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="layout-overlay layout-menu-toggle"></div>
      </div>
      <script src="{{asset('admin/assets/vendor/libs/jquery/jquery.js')}}"></script>
      <script src="{{asset('admin/assets/vendor/libs/popper/popper.js')}}"></script>
      <script src="{{asset('admin/assets/vendor/js/bootstrap.js')}}"></script>
      <script src="{{asset('admin/assets/vendor/js/menu.js')}}"></script>
      <script src="{{asset('admin/assets/js/main.js')}}"></script>
      <script type="text/javascript"> 
        
            var socket = new WebSocket('{{config("app.socket_url")}}?user_type=visitor&user_id={{Session::get("visitor_id")}}');

            socket.onopen = () => send_request();
            function send_request() {
               var data = {
                  type: "get-poll-result",
                  activity_id:'{{$activity_id}}',
               };
               socket.send(JSON.stringify(data));
            }
        
      </script>
   </body>
</html>