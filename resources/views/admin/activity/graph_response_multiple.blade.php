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
   .cl-bar-container 
   {
      width: 45px;
      height: 220px;
      background-color: #e7e7e7;
      position: relative;
      border-radius: 5px;
   }
   .bar2 
   {
      background: #cfcfcf;
      margin-bottom: 10px;
      padding: 10px;
   }
   @media (max-width:768px)
   {
      .mtb-2
      {
         margin-top: 3rem;
      }
   }
   .slider-container {
        overflow: hidden;
        width: 100%;
        margin: 0 auto;
    }
    .slider-wrapper {
        display: flex;
        transition: transform 1.8s ease;
    }
    .slider-item {
        flex: 0 0 100%; /* Occupies full width of container */
        padding: 20px;
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
                        <div class="col-md-12 col-12" id="graph_1_body">
                        </div>
                     </div>
                     <div class="row all-chart" id="chartno_2" style="display:none;">
                        <div class="custom-container">
                           <table class="custom-table" align="center" id="graph_2_body">
                           </table>
                        </div>
                     </div>
                     <div class="row mx-auto all-chart" id="chartno_3" style="display:none;" align="center">
                        @foreach($sub_activity_data as $value)
                           <div class="col-md-12 col-12"><br><br><br></div>
                           <!-- <div class="col-md-12 col-12"> -->
                              <label class="option">{{$value->question}}</label>
                              <div id="donutchart_{{$value->id}}" style="width: 100%; height: 100%;"></div>
                           <!-- </div> -->
                        @endforeach
                     </div>
                  </div> 
                  <div class="col-md-12" id="instructions"  style="display:none;">
                     <div class="row mx-auto">
                        <div class="col-md-12 col-12">
                           <div class="slider-container">
                              <div class="slider-wrapper">
                                 @foreach($option_data as $value)
                                    <div class="slider-item">
                                       <div class="row">
                                          <div class="col-md-6 col-6">{{$value['question_name']}}</div>
                                          <div class="col-md-6 col-6">
                                             <div class="row">
                                                @foreach($value['options'] as $value_1)
                                                   <div class="col-md-12 col-12 bar2">
                                                      {{$value_1}}
                                                   </div>
                                                @endforeach
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 @endforeach
                              </div>
                           </div>
                           <div class="text-center mt-3">
                              <button class="btn btn-primary" id="prevBtn">{{__('visitors.previous')}}</button>
                              <button class="btn btn-primary" id="nextBtn">{{__('visitors.next')}}</button>
                          </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row" style="margin-top: 5rem;">
                  <div class="col-6 col-md-6">
                     <label class="responsechange"><span class="toggle" id="badge_1" onclick="toggle_view(1)">{{__('admin.instructions')}}</span><span class="toggle toggle-active" id="badge_2" onclick="toggle_view(2)">{{__('admin.responses')}}</span></label>
                  </div>
                  <div class="col-6 col-md-6 mtb-2" align="right">
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
                                       @php $qno = 1; @endphp
                                       @foreach($sub_activity_data as $value)
                                          <div class="mb-3 col-md-12 col-12">
                                             <a href="javascript:void(0)" onclick="copy_embed_script('{{app_encode($value->id)}}','{{$qno}}')" id="copy_embed_script_{{$qno}}">{{__('admin.question')}} {{$qno}} - {{__('admin.copy_embed_script')}}</a>
                                          </div>
                                          @php $qno++; @endphp
                                       @endforeach
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
         var graph_1             = '';
         var graph_2             = '';
         var question_array      = data.question_array;
         var activity_data       = data.activity_data;
         var total_image         = 0;
         var is_responsed        = 0;

         for (var i = 0; i < question_array.length; i++) 
         {
            var array            = question_array[i];
            var width_1          = "95%";
            var width_2          = "5%";
            var img_class        = "";
            var onclick          = "";
            var percentage       = 0;
            var optionsArray     = array.options;

            graph_1              += '<table class="table" width="100%" style="background: #efefef;">';
            graph_2              += '<table class="custom-table" width="60%" align="center">';

            graph_1              += '<tr>';
            graph_1              += '<td colspan="2" align="center"><label class="option">'+array.question+'</label></td>';
            graph_1              += '</tr>';

            graph_2              += '<tr>';
            graph_2              += '<td colspan="'+optionsArray.length+'"><br></td>';
            graph_2              += '</tr>';

            graph_2              += '<tr>';
            graph_2              += '<td colspan="'+optionsArray.length+'" align="center"><label class="option">'+array.question+'</label></td>';
            graph_2              += '</tr>';

            graph_2              += '<tr>';
            graph_2              += '<td colspan="'+optionsArray.length+'"><br></td>';
            graph_2              += '</tr>';

            graph_2              += '<tr style="background: #efefef;">';

            for (var j = 0; j < optionsArray.length; j++) 
            {
               var subArray      = optionsArray[j];
               graph_1           += "<br>";
            
               var total_responses  = 0;
               if(array.select_count)
               {
                  total_responses = array.select_count;
               }
               if(total_responses>0)
               {
                  percentage = Math.round((subArray.select_count/total_responses)*100);
                  is_responsed++;
               }
               if(percentage<0)
               {
                  percentage = 0;
               }
               var option_text_name = subArray.option;
               if(option_text_name=="null" || option_text_name==null)
               {
                  option_text_name = "";
               }

               var score      = (subArray.score*subArray.select_count);
               var score_text = "";
               if(subArray.select_count<0)
               {
                  score_text     = ' (Votes : 0)';
               }
               else
               {
                  score_text     = ' (Votes : '+subArray.select_count+')';
               }

               graph_1        += '<tr>';

               graph_1        +='</td><td width="'+width_1+'" align="left"><label class="option">'+option_text_name+score_text+'</label><div class="bar-container"><div class="bar bar-fill" style="width: '+percentage+'%;"></div></div></td><td width="'+width_2+'" align="left"><span class="percentage">'+percentage+'%</span></td>';
               graph_1        += '</tr>';

               graph_2        += '<td align="center" width="30%"><span class="percentage">'+percentage+'%</span><div class="cl-bar-container"><div class="cl-bar cl-bar-fill" style="height: '+percentage+'%;"></div></div><label class="option" style="margin-top: 5px;">'+option_text_name+score_text+'</label></td>';
            }
            graph_1           += '</table>';
            graph_2           += '</tr></table>';
         }
         $("#graph_1_body").html(graph_1);
         $("#graph_2_body").html(graph_2);
         drawChart(question_array,activity_data);
         toggle_graph(map_type);
         if(is_responsed==0)
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
      
      function drawChart(question_array,activity_data) {
         
         var array = [['Option', 'Count']];
         if(question_array)
         {
            for (var s = 0; s < question_array.length; s++) 
            {
               var main_array    = question_array[s];
               var option_data   = main_array.options;
               var array         = [['Option', 'Count']];
               if(option_data)
               {
                  for (var i = 0; i < option_data.length; i++) 
                  {
                     var array_option     = option_data[i];
                     var total_responses  = 0;
                     var percentage       = 0;
                     if(main_array.select_count)
                     {
                        total_responses = main_array.select_count;
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
                     array.push([array_option.option+score_text, percentage]);
                  }
                  var data = google.visualization.arrayToDataTable(array);
                  var options = {
                     pieHole: 0.3,
                     colors: generate_color(array.length),
                     legend: {
                        position: 'top'
                     },
                  };
                  var chart = new google.visualization.PieChart(document.getElementById('donutchart_'+main_array.question_id));
                  chart.draw(data, options);
               }
            }
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
      function decreaseBrightness(rgb, factor) {
         return {
            r: Math.max(0, Math.floor(rgb.r * (1 - factor))),
            g: Math.max(0, Math.floor(rgb.g * (1 - factor))),
            b: Math.max(0, Math.floor(rgb.b * (1 - factor)))
         };
      }
      
      function generate_color(number) {
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
      
      function copy_embed_script(option_id="",qno="")
      {
         $("#copy_embed_script_"+qno).text("Copied!");
         var newcode = '<iframe src="{{url("/")}}/poll-result-multiple/@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset/'+option_id+'" width="800px" height="600px"></iframe>';
         copyToClipboard(newcode);
         setTimeout(function(){
            $("#copy_embed_script_"+qno).text("{{__('admin.question')}} "+qno+" - {{__('admin.copy_embed_script')}}");
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
   $(document).ready(function () {
        const sliderWrapper = document.querySelector('.slider-wrapper');
        const sliderItems = document.querySelectorAll('.slider-item');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        let currentIndex = 0;

        // Function to update the slider position
        function updateSlider() {
            const itemWidth = sliderItems[0].offsetWidth;
            sliderWrapper.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
        }

        // Event listener for "Next" button
        nextBtn.addEventListener('click', function () {
            if (currentIndex < sliderItems.length - 1) {
                currentIndex++;
            } else {
                currentIndex = 0; // Loop back to the first div
            }
            updateSlider();
        });

        // Event listener for "Previous" button
        prevBtn.addEventListener('click', function () {
            if (currentIndex > 0) {
                currentIndex--;
            } else {
                currentIndex = sliderItems.length - 1; // Loop to the last div
            }
            updateSlider();
        });
    });
</script>
@endpush