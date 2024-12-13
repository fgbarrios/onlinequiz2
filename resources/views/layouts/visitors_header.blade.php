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
      <style type="text/css">
         .bg-menu-theme {
         background-color: #211c4e !important;
         color: #697a8d !important;
         }
         .bg-menu-theme .menu-link,
         .bg-menu-theme .menu-horizontal-prev,
         .bg-menu-theme .menu-horizontal-next {
         color: #ffffff;
         }
         .bg-menu-theme .menu-link:hover,
         .bg-menu-theme .menu-horizontal-prev:hover,
         .bg-menu-theme .menu-horizontal-next:hover {
         color: #aba3a3;
         }
         .bg-menu-theme .menu-inner>.menu-item.active>.menu-link {
         color: #ffffff !important;
         background-color: rgb(255 255 255 / 16%) !important;
         }
         .invalid-data {
         color: #ff5151;
         font-size: 13px;
         margin: 5px;
         font-weight: 500;
         }
         .menu-vertical {
         width: 5rem;
         }
         .menu-vertical,
         .menu-vertical .menu-block,
         .menu-vertical .menu-inner>.menu-item,
         .menu-vertical .menu-inner>.menu-header {
         width: 5rem;
         }
         .layout-menu-fixed:not(.layout-menu-collapsed) .layout-page,
         .layout-menu-fixed-offcanvas:not(.layout-menu-collapsed) .layout-page {
         padding-left: 5rem;
         }
         @media only screen and (max-width: 1200px) {
         .layout-menu-fixed:not(.layout-menu-collapsed) .layout-page,
         .layout-menu-fixed-offcanvas:not(.layout-menu-collapsed) .layout-page {
         padding-left: 0rem;
         }
         }
         .layout-navbar.navbar-detached {
         width: 100%;
         margin: 0;
         border-radius: 0rem;
         padding: 0 1.5rem;
         }
         .layout-navbar {
         background-color: rgb(33 28 78) !important;
         -webkit-backdrop-filter: saturate(200%) blur(6px);
         backdrop-filter: saturate(200%) blur(6px);
         }
      </style>
      <script src="{{asset('admin/assets/vendor/js/helpers.js')}}"></script>
      <script src="{{asset('admin/assets/js/config.js')}}"></script>
   </head>
   <body>
      <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
               <ul class="menu-inner py-1">
                  <li class="menu-item">
                     <a href="javascript:void(0)" class="menu-link">
                     <i class='bx bxs-bar-chart-square' style="font-size: 2rem !important;"></i>
                     </a>
                  </li>
               </ul>
            </aside>
            <div class="layout-page">
               <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar" >
                  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                     <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                     <i class="bx bx-menu bx-sm"></i>
                     </a>
                  </div>
                  <div class="navbar-nav-left" id="navbar-collapse">
                     @if($name)<label style="margin-bottom: 0rem;font-size: 18px;font-weight: 500;">{{__('visitors.respond_as')}}</label><label class="card-title text-primary" style="margin-bottom: 0rem;margin-left: 0.3rem;font-size: 16px;font-weight: 500;">{{$name}}</label>@endif
                  </div>
               </nav>
               <div class="content-wrapper">
                  <div class="container-xxl flex-grow-1 container-p-y">
                     <div class="container-fluid vh-100" style="margin-top:10px" id="socket_response">
                        @yield('content')
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
         @if(Session::get('unique_name'))

         let added_response            = [];
         let removed_response          = [];
         let added_multiple_response   = [];
         let removed_multiple_response = [];

         var socket = new WebSocket('{{config("app.socket_url")}}?user_type=visitor&user_id={{Session::get("visitor_id")}}');

         socket.onmessage = function (e) {
            if (IsJsonString(e.data)) {
               send_request();
            } else {
               $("#socket_response").html(e.data);
            }
         }
         // socket.onopen = () => send_request();
         function send_request() {
            var data = {
               type: "poll-check-state",
               visitor_id: "{{Session::get('visitor_id')}}"
            };
            socket.send(JSON.stringify(data));
         }
         @endif

         @isset($response_data)
         var details = < ? php echo json_encode($response_data) ? > ;
         @else
         var details = {};
         @endisset


         // Save select option

         function select_option(id) {
            if (id != "") {
               var may_respond_count = parseInt($("#may_respond_count").val());
               var may_select_count = parseInt($("#may_select_count").val());
               var total_respond_count = parseInt($("#total_respond_count").val());
               var available_select = parseInt($("#available_select_" + id).val());
               var current_select = parseInt($("#current_select_" + id).val());

               if (total_respond_count < may_respond_count) {
                  if (available_select > 0) {
                     var available_select = available_select - 1;
                     var current_select = current_select + 1;
                     var temp = {};

                     temp['option'] = id;
                     temp['may_select_count'] = may_select_count;
                     temp['available_select'] = available_select;
                     temp['total_select'] = current_select;
                     details['option_' + id] = temp;

                     $("#available_select_" + id).val(available_select);
                     $("#current_select_" + id).val(current_select);
                     $("#total_respond_count").val(total_respond_count + 1);
                     $("#vote_count_" + id).text(current_select);
                     if (current_select > 0) {
                        $("#tr_" + id).attr("class", "bar active");
                        $("#delete_vote_" + id).show();
                     } else {
                        $("#tr_" + id).attr("class", "bar");
                        $("#delete_vote_" + id).hide();
                     }
                     // save_selected_option(id);
                     added_response.push(id);
                  }
               }
            }
         }

         // Remove select option

         function remove_option(id) {
            if (id != "") {
               var may_respond_count = parseInt($("#may_respond_count").val());
               var may_select_count = parseInt($("#may_select_count").val());
               var total_respond_count = parseInt($("#total_respond_count").val());
               var available_select = parseInt($("#available_select_" + id).val());
               var current_select = parseInt($("#current_select_" + id).val());
               if (current_select > 0) {
                  var available_select = available_select + 1;
                  var current_select = current_select - 1;
                  var temp = {};

                  temp['option'] = id;
                  temp['may_select_count'] = may_select_count;
                  temp['available_select'] = available_select;
                  temp['total_select'] = current_select;
                  details['option_' + id] = temp;

                  $("#available_select_" + id).val(available_select);
                  $("#current_select_" + id).val(current_select);
                  $("#total_respond_count").val(total_respond_count - 1);
                  $("#vote_count_" + id).text(current_select);
                  if (current_select > 0) {
                     $("#tr_" + id).attr("class", "bar active");
                     $("#delete_vote_" + id).show();
                  } else {
                     $("#tr_" + id).attr("class", "bar");
                     $("#delete_vote_" + id).hide();
                  }
                  // remove_selected_option(id);
                  removed_response.push(id);
               }
            }
         }

         // Check option is already selected

         function check_option_already_selected(object, option, id) {
            return Object.values(object).some(v => v.option === id);
         }

         function IsJsonString(str) {
            try {
               var json = JSON.parse(str);
               return (typeof json === 'object');
            } catch (e) {
               return false;
            }
         }

         function add_voting(question_id = "", id = "",ref="") {
            var activity_id      = $("#activity_id").val();
            var available_count  = $("#available_count_"+ref).val();
            if (activity_id != "" && question_id != "" && id != "" && available_count>0) 
            {
               $(".bar_"+id).attr("class","row bar1 bar2 active bar_"+id);
               $("#delete_div_"+id).show();
               available_count--;
               $("#available_count_"+ref).val(available_count);
               var temp = {};
               temp['question_id'] = question_id;
               temp['option_id'] = id;
               added_multiple_response.push(temp);
            }
         }

         function remove_voting(question_id = "", id = "",ref="") {
            var activity_id = $("#activity_id").val();
            var available_count  = $("#available_count_"+ref).val();
            if (activity_id != "" && question_id != "" && id != "") {
               var temp = {};
               temp['question_id'] = question_id;
               temp['option_id'] = id;
               removed_multiple_response.push(temp);
               $(".bar_"+id).attr("class","row bar1 bar2 bar_"+id);
               $("#delete_div_"+id).hide();
               available_count++;
               $("#available_count_"+ref).val(available_count);
            }
         }
         let is_submit = 0;
         function submit_answers() {

            if(is_submit==0)
            {
               $("#submit_btn").text("Please wait..");
               is_submit++;
               var activity_id = $("#activity_id").val();
               $.ajax({
                  url: "{{route('save-visitors-response')}}",
                  type: "POST",
                  data: {
                     "_token": "{{csrf_token()}}",
                     "activity_id": activity_id,
                     "added_response": added_response,
                     "removed_response": removed_response
                  },
                  dataType: 'json',
                  beforeSend: function () {},
                  success: function (data) {
                     added_response    = [];
                     removed_response  = [];
                     var data = {
                        type: "get-poll-result",
                        activity_id: activity_id,
                     };
                     socket.send(JSON.stringify(data));

                     var data = {
                        type: "thank-you",
                        visitor_id: '{{Session::get("visitor_id")}}',
                     };
                     socket.send(JSON.stringify(data));
                     $("#submit_btn").text("Submit");
                  },
                  error: function (e) {
                     is_submit=0;
                     $("#submit_btn").text("Submit");
                  }
               });
            }
         }
         let is_submit_multiple = 0;
         function submit_answers_multiple()
         {
            if(is_submit_multiple==0)
            {
               $("#submit_btn").text("Please wait..");
               is_submit_multiple++;
               var activity_id = $("#activity_id").val();
               $.ajax({
                  url: "{{route('save-visitors-response-multiple')}}",
                  type: "POST",
                  data: {
                     "_token": "{{csrf_token()}}",
                     "activity_id": activity_id,
                     "added_response": added_multiple_response,
                     "removed_response": removed_multiple_response
                  },
                  dataType: 'json',
                  beforeSend: function () {},
                  success: function (data) {
                     added_multiple_response    = [];
                     removed_multiple_response  = [];
                     var data = {
                        type: "get-poll-result",
                        activity_id: activity_id,
                     };
                     socket.send(JSON.stringify(data));
                     var data = {
                        type: "thank-you",
                        visitor_id: '{{Session::get("visitor_id")}}',
                     };
                     socket.send(JSON.stringify(data));
                     $("#submit_btn").text("Submit");
                  },
                  error: function (e) {
                     is_submit_multiple = 0;
                     $("#submit_btn").text("Submit");
                  }
               });
            }
         }
      </script>
   </body>
</html>