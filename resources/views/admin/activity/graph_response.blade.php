@extends('layouts.header')
@section('content')
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
   height: 100%;
   flex-direction: column;
   justify-content: space-between;
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
</style>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="card">
      <div class="card-body">
         <div class="row">
            <div class="col-md-8">
               <div class="row" align="center" id="title_code">
                  <div class="col-md-12">
                     <h4>@isset($activity_data[0]->title){{$activity_data[0]->title}}@endisset</h4>
                     @isset($activity_data[0]->title_image) @if($activity_data[0]->title_image)
                     <a href="{{asset('/').'/'.$activity_data[0]->title_image}}" target="_blank"><img src="{{asset('/').'/'.$activity_data[0]->title_image}}" class="title-image"></a>@endif
                     @endisset
                  </div>
               </div>
               <br>
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
               <div class="row" style="margin-top: 5rem;">
                  <div class="col-6 ">
                     <label class="responsechange"><span class="toggle" id="badge_1" onclick="toggle_view(1)">{{__('admin.instructions')}}</span><span class="toggle toggle-active" id="badge_2" onclick="toggle_view(2)">{{__('admin.responses')}}</span></label>
                  </div>
                  <div class="col-6" align="right">
                     <a href="javascript:void(0)" onclick="clear_response('@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset')" class="btn btn-primary">{{__('admin.clear_response')}}</a>
                  </div>
               </div>
            </div>
            <div class="col-md-4 p-0">
               <div class="row box-shadow align-justy p-0">
                  <div class="p-0">
                     <div class="col-md-12">
                        <div class="accordion accordion-flush" id="accordion_1">
                           <div class="accordion-item">
                              <h2 class="accordion-header" id="flush-accordion_1">
                                 <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                 {{__('admin.virtual_settings')}}
                                 </button>
                              </h2>
                              <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-accordion_1" data-bs-parent="#accordion_1">
                                 <div class="accordion-body">
                                    <div class="row">
                                       <div class="col-md-3 graph graph-active" align="center" id="graph_1" onclick="toggle_graph(1)" title="Bar Chart">
                                          <img src="https://static.thenounproject.com/png/1521123-200.png" class="bar-icon">
                                       </div>
                                       <div class="col-md-3 graph" align="center" id="graph_2" onclick="toggle_graph(2)" title="Column Chart">
                                          <img src="https://i.pinimg.com/564x/f0/6a/17/f06a178d7ed9c1270f63a85b183bcb49.jpg" class="bar-icon">
                                       </div>
                                       <div class="col-md-3 graph" align="center" id="graph_3" onclick="toggle_graph(3,'manual')" title="Donut Chart">
                                          <img src="https://cdn-icons-png.flaticon.com/512/481/481819.png" class="bar-icon">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="accordion accordion-flush" id="accordion_2">
                           <div class="accordion-item">
                              <h2 class="accordion-header" id="flush-accordion_2">
                                 <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                 {{__('admin.share_and_embed')}}
                                 </button>
                              </h2>
                              <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-accordion_2" data-bs-parent="#accordion_2">
                                 <div class="accordion-body">
                                    <div class="row">
                                       <div class="mb-3 col-md-12 col-12">
                                          <label class="form-label">{{__('admin.response_link')}}</label>
                                          <p>{{__('admin.response_para')}}</p>
                                          <a href="javascript:void(0)" onclick="copy_url('{{url("/")}}/visit/{{Auth::user()->username}}')" id="copy_url">{{__('admin.copy_response_link')}}</a>
                                       </div>
                                       <div class="mb-3 col-md-12 col-12">
                                          <p>{{__('admin.embed_para')}}</p>
                                       </div>
                                       <div class="mb-3 col-md-12 col-12">
                                          <a href="javascript:void(0)" onclick="copy_embed_script()" id="copy_embed_script">{{__('admin.copy_embed_script')}}</a>
                                       </div>
                                       <div class="mb-3 col-md-12 col-12">
                                          <label class="form-label">{{__('admin.share_qr_code')}}</label>
                                          <br><br>
                                          {{QrCode::size(150)->generate(url("/").'/visit/'.Auth::user()->username)}}
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="actionBtn">
                     <div class="EditBtn">
                        <a href="{{url('/')}}/edit-activity/@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset" id="edit_btn" class="btn btn-primary">{{__('admin.edit')}}</a>
                     </div>
                     <div class="responseBtn">
                        <a href="{{url('/')}}/see-response/@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset" class="btn btn-secondary">{{__('admin.response_history')}}</a>
                     </div>
                     <div class="DeleteBtn">
                        <a href="javascript:void(0)" onclick="delete_activity('@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset')" class="btn btn-secondary">{{__('admin.delete')}}</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
   // $(function() {
   
      let resID      = "";
      let map_type   = 1;
      var socket     = new WebSocket('{{config("app.socket_url")}}?user_type=admin&user_id={{$admin_id}}');

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
            activity_id: '@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset',
         };
         socket.send(JSON.stringify(data));
      }
      
      function generate_graph_result(data) 
      {
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
   
            // if(percentageArray['id_'+array.id])
            // {
            //    percentage = percentageArray['id_'+array.id];
            // }

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
               score_text = ' (Votes : 0)';
            }
            else
            {
               score_text = ' (Votes : '+array.select_count+')';
            }

            graph_1 += '<tr><td width="'+width_1+'" class="imgdiv '+img_class+'">';
            graph_2 += '<td align="center"><span class="percentage">'+percentage+'%</span><div class="cl-bar-container"><div class="cl-bar cl-bar-fill" style="height: '+percentage+'%;"></div></div><label class="option" style="margin-top: 5px;">'+option_text_name+score_text+'</label><div class="imgdiv">';
      
            if(array.option_image)
            {
               var url = '{{asset("/")}}/'+array.option_image;
               graph_1 += '<a href="'+url+'" target="_blank"><img src="'+url+'" class="title-image"></a>';
               graph_2 += '<a href="'+url+'" target="_blank"><img src="'+url+'" class="optionimage"></a>';
               total_image++;
            }
      
            graph_1 +='</td><td width="'+width_2+'" align="left"><label class="option">'+option_text_name+score_text+'</label><div class="bar-container"><div class="bar bar-fill" style="width: '+percentage+'%;"></div></div></td><td width="'+width_3+'" align="left"><span class="percentage">'+percentage+'%</span></td></tr>';
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
         drawChart(option_data,activity_data);
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
      
      google.charts.load("current", {packages: ["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      
      function drawChart(option_data,activity_data) 
      {
         var array = [['Option', 'Count']];
         if(option_data)
         {
            for (var i = 0; i < option_data.length; i++) {
               var array_option = option_data[i];
               var total_responses = 0;
               var percentage = 0;
               if(activity_data[0].total_responses)
               {
                  total_responses = activity_data[0].total_responses;
               }
               if (total_responses > 0) {
                  percentage = Math.round((array_option.select_count / total_responses) * 100);
               }
   
               var score = (array_option.score*array_option.select_count);
               var score_text = "";
               if(activity_data[0].is_had_score=="1")
               {
                  score_text = ' (Score : '+score+')';
               }
               if(array_option.option_image)
               {
                  array.push(['Image', percentage]);
               }
               else
               {
                  array.push([array_option.option+score_text, percentage]);
               }
            }
            var data = google.visualization.arrayToDataTable(array);
            var options = {
               pieHole: 0.3,
               colors: generate_color(array.length),
               legend: {
                  position: 'top'
               },
            };
            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);
         }
      }
      
      // Convert a hex color code to an RGB color object
      function hexToRgb(hex) {
         hex = hex.replace(/^#/, '');
         const bigint = parseInt(hex, 16);
         const r = (bigint >> 16) & 255;
         const g = (bigint >> 8) & 255;
         const b = bigint & 255;
         return {
            r,
            g,
            b
         };
      }
      
      // Convert an RGB color object to a hex color code
      function rgbToHex(rgb) {
         return `#${(1 << 24 | rgb.r << 16 | rgb.g << 8 | rgb.b).toString(16).slice(1)}`;
      }
      
      // Decrease brightness by a factor (0 to 1)
      function decreaseBrightness(rgb, factor) 
      {
         return {
            r: Math.max(0, Math.floor(rgb.r * (1 - factor))),
            g: Math.max(0, Math.floor(rgb.g * (1 - factor))),
            b: Math.max(0, Math.floor(rgb.b * (1 - factor)))
         };
      }
      
      function generate_color(number) 
      {
         const baseColor = "4387dd"; // Base color
         const baseRgb = hexToRgb(baseColor);
      
         const brightnessFactorStep = 0.1; // Step to decrease brightness
         var colors = ['#4387dd'];
         for (let i = 0; i < number; i++) {
            const brightnessFactor = brightnessFactorStep * (i + 1);
            const newRgb = decreaseBrightness(baseRgb, brightnessFactor);
            const newColor = rgbToHex(newRgb);
            colors.push(newColor);
         }
         return colors;
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
      
      function copy_url(url)
      {
         $("#copy_url").text("Copied!");
         copyToClipboard(url);
         setTimeout(function(){
            $("#copy_url").text("{{__('admin.copy_response_link')}}");
         },3000);
      }
      
      function copy_embed_script()
      {
         $("#copy_embed_script").text("Copied!");
         var newcode = '<iframe src="{{url("/")}}/poll-result/@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset" width="800px" height="600px"></iframe>';
         copyToClipboard(newcode);
         setTimeout(function(){
            $("#copy_embed_script").text("{{__('admin.copy_embed_script')}}");
         },3000);
      }
   
      function toggle_view(type) 
      {
         $(".toggle").attr("class","toggle");
         $("#badge_"+type).attr("class","toggle toggle-active");
         if(type==1)
         {
            $("#instructions").show();
            $("#graphs").hide();
         }
         else if(type==2)
         {
            $("#graphs").show();
            $("#instructions").hide();
         }
      }
   
      function delete_activity(ref) {
       if (confirm('{{__("admin.delete_confirm_alert")}}')) {
           $.ajax({
               url: "{{route('delete-activity')}}",
               type: "POST",
               data: {
                   'ref': ref,
                   '_token': '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function(data) {
                   if (data.code == 200) {
                     call_visitors_socket();
                     window.location.href="{{url('/')}}/list-activity";
                   }
               },
           });
       }
      }
   
      function clear_response(ref) {
       if (confirm('{{__("admin.clear_response_confirm")}}')) {
           $.ajax({
               url: "{{route('clear-activity-response')}}",
               type: "POST",
               data: {
                   'ref': ref,
                   '_token': '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function(data) {
                   if (data.code == 200) {
                     call_visitors_socket();
                   }
               },
           });
       }
      }
   
      function call_visitors_socket()
      {
         var data = {
            type: "get-poll-result",
            activity_id: '@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset',
         };
         socket.send(JSON.stringify(data));
         
         var data = {
            type: "get-poll-result",
            resID: resID,
            adminid: '{{$admin_id}}',
            activity_id:'@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset',
         };
         socket.send(JSON.stringify(data));
   
         var data = {
            type: "poll-check-state",
            visitor_id : '@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset',
         };
         socket.send(JSON.stringify(data));
      }
   
      function calculate_percentage(totalVote,options)
      {
         var optionsArray = [];
         for (var i = 0; i < options.length; i++) 
         {
            var temp       = {};
            var array      = options[i];
            temp['name']   = 'id_'+array.id;
            temp['votes']  = array.select_count;
            optionsArray.push(temp);
         }
         var options = optionsArray;
         var result        = [];
         // Calculate the total votes
         const totalVotes = options.reduce((sum, option) => sum + option.votes, 0);
   
         // Calculate the total percentage points
         let totalPercentage = 0;
   
         // Calculate the percentage and update the options array
         options.forEach(option => {
           option.percentage = Math.floor((option.votes / totalVotes) * 100);
           totalPercentage += option.percentage;
         });
   
         // Calculate the remaining percentage points to reach 100
         const remainingPercentage = 100 - totalPercentage;
   
         // Distribute the remaining percentage evenly among options
         for (let i = 0; i < remainingPercentage; i++) {
           options[i % options.length].percentage++;
         }
   
         // Output the results
         options.forEach(option => {
           result[option.name] = option.percentage;
         });
   
         return result;
      }
   // });
   
      function copyToClipboard(text) {
   
     // Create a "hidden" input
     var aux = document.createElement("input");
   
     // Assign it the value of the specified element
     aux.setAttribute("value", text);
   
     // Append it to the body
     document.body.appendChild(aux);
   
     // Highlight its content
     aux.select();
   
     // Copy the highlighted text
     document.execCommand("copy");
   
     // Remove it from the body
     document.body.removeChild(aux);
   
   }
</script>
@endpush