<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{asset('admin/assets/')}}" data-template="vertical-menu-template-free">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
      <title>{{env('APP_NAME')}}</title>
      <link href="{{asset('admin/assets/Akshar-Regular.ttf')}}" rel="stylesheet">
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
      <style type="text/css">
         @font-face {
         font-family: 'Akshar';
         src: url('{{asset('admin/assets/Akshar-Regular.ttf')}}') format('truetype');
         font-weight: 400;
         font-style: normal;
         }
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
      <!-- / Menu -->
      <!-- Layout container -->
      <div class="layout-page" style="padding: 0px;">
      <!-- / Navbar -->
      <!-- Content wrapper -->
      <div class="content-wrapper ">
         <link rel="stylesheet" href="{{asset('admin/assets/css/activity_graph.css')}}">
         <!-- Content -->
         <style>
            .actionBtn{
            display: flex;
            justify-content: space-around;
            margin-bottom: 17px;
            flex-wrap: wrap;
            gap: 4px;
            }
            .align-justy{
            /*height: 100%;
            flex-direction: column;
            justify-content: space-between;*/
            }
            .accordion-button {
            background-color: #f7f7ff;
            }
            label.responsechange {
            background-color: #5B5B5B;
            padding: 4px 0px;
            border-radius: 100px;
            }
            .toggle {
            background: transparent;
            }
            .toggle-active {
            background: #3d5599 !important;
            }
            .option {
            font-weight: 500;
            margin-bottom: 0px;
            line-break: normal;
            font-size: 15px;
            }
            .title-image {
            display: block;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 30vh;
            margin: 0 auto;
            }
            .card-body {
            flex: 1 1 auto;
            padding: 0rem 1.5rem;
            }
         </style>
         <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
               <div class="card-body">
                  <div class="row" id="full_page_response" style="display:none;">
                     <div class="col-md-12">
                        <div class="row">
                           <div class="col-md-12" id="graphs">
                              <div class="row all-chart" id="chartno_1" style="display:none;">
                                 <table class="table" width="100%" id="graph_1_body">
                                 </table>
                              </div>
                              <div class="row all-chart" id="chartno_2" style="display:none;">
                                 <div class="custom-container">
                                    <table class="custom-table" align="center" id="graph_2_body">
                                    </table>
                                 </div>
                              </div>
                              <div class="row mx-auto all-chart" id="chartno_3" style="display:none;" align="center">
                                 <div id="donutchart" style="width: 100%; height: 100%;"></div>
                              </div>
                           </div>
                           <div class="col-md-12" id="instructions"  style="display:none;">
                              <div class="row mx-auto">
                                 <div class="col-md-12 col-12">
                                    <h4>@isset($activity_data[0]->title){{$activity_data[0]->title}}@endisset</h4>
                                 </div>
                                 <div class="col-md-12 col-12">
                                    <table class="table mt-3" width="100%">
                                       <tbody>
                                          @foreach($option_data as $value)
                                          <tr class="table-bar">
                                             @isset($activity_data[0]->is_had_score)
                                             @if($activity_data[0]->is_had_score==1)
                                             <td width="5%" class="pointer">
                                                <div class="box">0</div>
                                             </td>
                                             @endif
                                             @endisset
                                             <td width="95%" class="pointer">@if($value->option_image!="")<img src="{{asset($value->option_image)}}" alt="Option Image" class="option-image">@else<label class="options">{{$value->option}}</label>@endif</td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row" id="full_page_empty" style="display:none;">
                     <center>
                        <h4>{{__('admin.active_status_2')}}</h4>
                     </center>
                  </div>
                  <div class="content-backdrop fade"></div>
               </div>
               <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
         </div>
         <!-- Overlay -->
         <div class="layout-overlay layout-menu-toggle"></div>
      </div>
      <script src="{{asset('admin/assets/vendor/libs/jquery/jquery.js')}}"></script>
      <script type="text/javascript">
         // $(function() {
         
            let resID      = "";
         let map_type   = 1;
         var socket     = new WebSocket('{{config("app.socket_url")}}?user_type=admin&user_id={{$admin_id}}');
         
         socket.onopen = function(e) {
         };
         
         socket.onerror = function(e) {
         };
         
         socket.onmessage = function (e) {
         var data = JSON.parse(e.data);
         if(data.type=="id")
         {
         resID = data.data;
         send_request();
         }
         else
         {
         generate_graph_result(data.data);
         }
         }
         // socket.onopen = () => send_request();
         function send_request() {
         var data = {
         type: "get-poll-result",
         resID: resID,
         adminid: '{{$admin_id}}',
         activity_id: @isset($activity_data[0]->id)'{{app_encode($activity_data[0]->id)}}'@endisset,
         };
         socket.send(JSON.stringify(data));
         }
            
            function generate_graph_result(data) 
            {
               if(data.option_data.length==0)
               {
                  $("#full_page_response").hide();
                  $("#full_page_empty").show();
               }
               else
               {
                  $("#full_page_response").show();
                  $("#full_page_empty").hide();
               }
               var graph_1 = '';
               var graph_2 = '';
               var graph_3 = '';
            
               var option_data   = data.option_data;
               var activity_data = data.activity_data;
               var total_image   = 0;
         
               // var percentageArray = calculate_percentage(activity_data.total_responses,option_data);
               graph_1  = '<table class="table" width="100%">';
               graph_2  = '<table class="custom-table" align="center"><tr>';
               
               for (var i = 0; i < option_data.length; i++) {
            
                  var array         = option_data[i];
                  var width_1       = "15%";
                  var width_2       = "95%";
                  var width_3       = "5%";
                  var img_class     = "";
                  var onclick       = "";
            
                  if(array.option_image)
                  {
                     img_class  = "option-image";
                  }
            
                  var total_responses  = 0;
                  var percentage       = 0;
            
                  if(activity_data[0].total_responses)
                  {
                     total_responses = activity_data[0].total_responses;
                  }
                  if(total_responses>0)
                  {
                     percentage = Math.round((array.select_count/total_responses)*100);
                  }
         
                  if(percentage<0)
                  {
                     percentage = 0;
                  }
         
                  var option_text_name = array.option;
                  if(option_text_name=="null" || option_text_name==null)
                  {
                     option_text_name = "";
                  }
                  var score = (array.score*array.select_count);
                  var score_text = "";
         
                  if(array.select_count<0)
                  {
                     score_text = '<br><label class="option">(Votes : 0)</label>';
                  }
                  else
                  {
                     score_text = '<br><label class="option">(Votes : '+array.select_count+')</label>';
                  }
                  
         
                  graph_1 += '<tr><td width="50%" align="left">';
         
                  graph_2 += '<td align="center"><span class="percentage">'+percentage+'%</span><div class="cl-bar-container"><div class="cl-bar cl-bar-fill" style="height: '+percentage+'%;"></div></div><label class="option" style="margin-top: 5px;">'+option_text_name+score_text+'</label><div class="imgdiv">';
            
                  if(array.option_image)
                  {
                     var url = '{{asset("/")}}/'+array.option_image;
                     graph_1 += '<a href="'+url+'" target="_blank"><img src="'+url+'" class="title-image"></a>'+score_text;
                     graph_2 += '<a href="'+url+'" target="_blank"><img src="'+url+'" class="optionimage"></a>';
                     total_image++;
                  }
                  else
                  {
                     graph_1 += '<label class="option">'+option_text_name+score_text+'</label>';
                  }
            
                  graph_1 +='</td><td width="45%" align="right"><div class="bar-container"><div class="bar bar-fill" style="width: '+percentage+'%;"></div></div></td><td width="5%" align="left"><span class="percentage">'+percentage+'%</span></td></tr>';
                  graph_2 +='</div></td>';
               }
            
               graph_1 += '</table>';
               graph_2 += '</tr></table>';
            
               $("#graph_1_body").html(graph_1);
               $("#graph_2_body").html(graph_2);
               if(total_image==0)
               {
                  $(".imgdiv").hide();
               }
               else
               {
                  $(".imgdiv").show();
               }
         
               toggle_graph(map_type);
               if(activity_data[0].total_responses==0)
               {
                  $("#edit_btn").show();
               }
               else
               {
                  $("#edit_btn").hide();
               }
            } 
            
            
            function toggle_graph(type,mode="")
            {
               map_type = type;
               $(".all-chart").hide();
               $(".graph").each(function(){
                  $(this).attr("class","col-md-3 graph");
               });
               $("#graph_"+type).attr("class","col-md-3 graph graph-active");
               $("#chartno_"+type).show();
               if(type==3 && mode=="manual")
               {
                  send_request();
               }
            }
            
      </script>
   </body>
</html>