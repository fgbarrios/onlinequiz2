@extends('layouts.header')
@section('content')
<style type="text/css">
   .option {
   background: #f1f1f1;
   padding: 10px 5px;
   }
   .fs-2 {
   font-size: 2rem;
   }
   .div-center {
   display: flex;
   align-items: center;
   }
   .correct-answer {
   background: green;
   color: white;
   }
   .option-img {
   display: block;
   height: auto;
   margin: 0 auto;
   max-height: 300px;
   max-width: 100%;
   min-height: 150px;
   min-width: 150px;
   -o-object-fit: contain;
   object-fit: contain;
   padding: 0.5em 0;
   -webkit-user-select: none;
   -moz-user-select: none;
   -ms-user-select: none;
   user-select: none;
   }
   /* .btn {
   margin-top: 0.5rem;
   } */
   .dropdown-toggle::after {
   display: none !important;
   }
   .over-flow_y {
   /*min-height: 1000px;
   max-height: 1000px;*/
   }
   .mt1
   {
   margin-top: 0rem;
   }
   @media (max-width:768px)
   {
   .mt1
   {
   margin-top: 1rem;
   }
   }
</style>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="card">
         <div class="card-body">
            <div class="row">
                @if($total_response>0)
                   <div class="col-md-2 mt1" align="left">
                      <div class="row">
                         <div class="col-md-12">
                            <form method="POST" id="share_responses" action="{{route('send-polling-results')}}">
                               @csrf
                               <button type="button" onclick="confirm_share_response()"
                                  class="btn btn-primary btn-icons">{{__('admin.share_responses')}}</button>
                            </form>
                         </div>
                      </div>
                   </div>
               @endif
               <div class="col-md-3 mt1" align="left">
                  <a href="{{route('add-activity')}}" class="btn btn-primary">{{__('admin.add_new')}}
                  {{__('admin.activity')}}</a>
               </div>
               <div class="@if($total_response==0){{'col-md-5'}}@else{{'col-md-3'}}@endif mt1"></div>
               <div class="col-md-4 mt1" align="right">
                  <input type="text" class="form-control" name="search" id="search" oninput="load_activity_date(1);" value="" placeholder="{{__('admin.search_activity')}}">
               </div>
            </div>
            <div class="row">
               <div class="col-md-12" align="center">
                  @if(session('success'))
                  <br>
                  <div class="alert alert-success" role="alert" align="center">
                     <label>{{session('success')}}</label>
                  </div>
                  @endif
                  @if ($errors->any())
                  <br>
                  <div class="alert alert-danger" role="alert" align="center">
                     <label>{{$errors->all()[0]}}</label>
                  </div>
                  @endif
               </div>
            </div>
            <div class="row">
               <div class="col-md-12" align="right"><br></div>
               <!-- <div class="table-responsive over-flow_y"> -->
               <table class="table table-hover" id="activity_id">
                  <thead>
                     <tr>
                        <th width="10%">{{__('admin.order')}}</th>
                        <th width="40%">{{__('admin.activity_name')}}</th>
                        <th width="15%">{{__('admin.last_modified')}}</th>
                        <th width="15%">{{__('admin.response_count')}}</th>
                        <th width="20%">{{__('admin.action')}}</th>
                     </tr>
                  </thead>
                  <tbody id="sorting-row">
                  </tbody>
               </table>
               <!-- </div> -->
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
   // Load function on page load
   
   $(document).ready(function() {
       initial_sortable();
       var socket = new WebSocket('{{config("app.socket_url")}}?user_type=admin&user_id={{auth::user()->id}}');
       var data = {
           type: "poll-check-state"
       };
       socket.onopen = () => socket.send(JSON.stringify(data));
       load_activity_date();
   });
   
   function change_status(id) {
       var status = "2";
       if ($("#switch_" + id).is(":checked") == true) {
           status = "1";
       }
       $.ajax({
           url: "{{route('change-activity-status')}}",
           type: "POST",
           data: {
               'ref': id,
               '_token': '{{csrf_token()}}',
               'status': status
           },
           dataType: 'json',
           success: function(data) {
               if (data.code == 200) {

                   location.reload();
               }
           },
       });
   }
   
   // Initiat options sortable
   
   function initial_sortable() {
       $("#sorting-row").sortable({
           placeholder: "sortable-placeholder",
       });
       $("#sorting-row").disableSelection();
   }
   
   // Update sorting order
   let old_order = [];
   
   function update_sort_order() {
       var order = 1;
       var array = [];
       $('.tr').each(function() {
           var id = $(this).data('id');
           var temp = {};
           temp['ref'] = id;
           temp['order'] = order;
           array.push(temp);
           order++;
       });
       if (old_order.length == 0) {
           old_order = array;
       } else {
           if (compareArraysOfObjects(array, old_order, 'ref') == false) {
               $.ajax({
                   url: "{{route('update-activity-sort-order')}}",
                   type: "POST",
                   data: {
                       'data': array,
                       '_token': '{{csrf_token()}}'
                   },
                   dataType: 'json',
                   success: function(data) {
                       if (data.code == 200) {
                           old_order = array;
                           location.reload();
                       }
                   },
               });
           }
       }
   }
   
   // Compare two array of objects with key
   
   function compareArraysOfObjects(array1, array2, key) {
       if (array1.length !== array2.length) {
           return false; // Arrays have different lengths, so they can't be equal
       }
       const sortedArray1 = array1.slice().sort((a, b) => a[key] - b[key]);
       const sortedArray2 = array2.slice().sort((a, b) => a[key] - b[key]);
       for (let i = 0; i < sortedArray1.length; i++) {
           if (sortedArray1[i][key] !== sortedArray2[i][key]) {
               return false; // Objects at the same index have different values for the specified key
           }
       }
       return true; // All objects have matching values for the specified key
   }
   
   // Check order modified
   
   setInterval(function() {
       update_sort_order();
   }, 1000);
   
   // Delete Activity
   
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
                        var socket = new WebSocket('{{config("app.socket_url")}}?user_type=admin&user_id={{auth::user()->id}}');
                        var data =  { type: "get-poll-result", activity_id: ref, };
                        socket.onopen = () => socket.send(JSON.stringify(data));

                        var data = {
                        type: "get-poll-result",
                        activity_id: ref,
                        };
                        socket.send(JSON.stringify(data));
                       location.reload();
                   }
               },
           });
       }
   }
   
   // Clone Activity
   
   function clone_activity(ref) {
       if (confirm('{{__("admin.clone_confirm_alert")}}')) {
           $.ajax({
               url: "{{route('clone-activity')}}",
               type: "POST",
               data: {
                   'ref': ref,
                   '_token': '{{csrf_token()}}'
               },
               dataType: 'json',
               success: function(data) {
                   if (data.code == 200) {
                       location.reload();
                   }
               },
           });
       }
   }
   
   function confirm_share_response() {
       if (confirm('{{__("admin.share_response_confirm_alert")}}')) {
           $("#share_responses").submit();
       }
   }
   let is_loaded   = 0;
   let from_load   = 0;
   let limit       = 40;
   var height      = 0;
   // function load_activity_date(type="")
   // {
   //     if(type==1)
   //     {
   //         $("#sorting-row").html("");
   //         from_load   = 0;
   //         height      = 0;
   //         is_loaded   = 0;
   //     }
   //     var search = $("#search").val();
   //     // if(is_loaded==0 && from_load < 40)
   //     // {
   //         $.ajax({
   //             url: "{{route('load-activity-data')}}",
   //             type: "POST",
   //             data: {
   //                 'from'  : from_load,
   //                 'limit' : limit,
   //                 'search': search,
   //                 '_token': '{{csrf_token()}}'
   //             },
   //             dataType: 'json',
   //             success: function(res) {
   //                 is_loaded++;
   //                 var html = "";
   //                 for (var i = 0; i < res.data.length; i++) 
   //                 {
   //                     var array = res.data[i];
   //                     html += '<tr class="tr" data-id="'+array.encode+'">';
   //                     html += '<td><span style="cursor:grabbing;"><i class="fa-solid fa-grip-vertical fa-xl"style="color: #c9c9c9;"></i>&nbsp; &nbsp;</span>'+array.sort_order+'</td>';
   //                     html += '<td style="cursor: pointer;" onclick="window.location.href=\'{{url("/")}}/see-graph-response/'+array.encode+'\'">'+array.title+'</td>';
   //                     html += '<td>'+array.last_change+'</td>';
   //                     html += '<td>'+array.total_responses+'</td>';
   
   //                     var checked = "";
   //                     if(array.status==1)
   //                     {
   //                         var checked = "checked";
   //                     }
   //                     var edit = '';
   //                     if(array.total_responses==0)
   //                     {
   //                         edit = '<a href="{{url('/')}}/edit-activity/'+array.encode+'"class="btn  btn-sm" title="{{__("admin.edit")}}"><i class="bx bxs-pencil"></i> {{__("admin.edit")}}</a>';
   //                     }
   //                     html += '<td class="d-flexs align-items-center"><div class="row"><div class="col-md-3 col-3"><div class="form-check form-switch" style="display:inline-block;"><input class="form-check-input" type="checkbox"onchange="change_status(\''+array.id+'\')" role="switch" id="switch_'+array.id+'" '+checked+'><label class="form-check-label" for="switch"></label></div></div><div class="col-md-9 col-9"><div class="dropdown"><button class="btn dropdown-toggle" type="button"id="dropdownMenuButton1" data-bs-toggle="dropdown"aria-expanded="false"><i class="fa-solid fa-ellipsis" style="color: #6C757D;"></i></button><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"><li>'+edit+'</li><li> <a href="javascript:void(0)" onclick="delete_activity(\''+array.encode+'\')" class="btn btn-sm"><i class="bx bx-trash" title="{{__("admin.delete")}}"></i> {{__("admin.delete")}}</a></li><li> <a href="javascript:void(0)" onclick="clone_activity(\''+array.encode+'\')" class="btn btn-sm" title="{{__("admin.clone")}}"><i class="bx bxs-copy"></i> {{__("admin.clone")}}</a></li><li><a href="{{url('/')}}/see-response/'+array.encode+'" class="btn  btn-sm" title="{{__("admin.view_response")}}"><i class="bx bx-message-alt-dots"></i> {{__("admin.view_response")}}</a></li></ul></div></div></div></td>';
   
   //                     html += '</tr>';
   //                 }
   //                 if(res.data.length==0)
   //                 {
   //                     $("#sorting-row").html("");
   //                     html += '<tr><td colspan="5" align="center">{{__("admin.no_data_available")}}</td></tr>';
   //                 }
   //                 $("#sorting-row").html("");
   //                 $("#sorting-row").append(html);
   
   //                 old_order   = [];
   //                 from_load   = from_load+limit+1;
   //                 height      = parseInt($('#activity_id').outerHeight());
   //                 is_stop     = 0;
   //             },
   //             error: function() {
   //             }
   //         });
   //     // }
   // }

   function load_activity_date(type="")
   {
       var search = $("#search").val();
       $.ajax({
           url: "{{route('load-activity-data')}}",
           type: "POST",
           data: {
               'from'  : "",
               'limit' : "",
               'search': search,
               '_token': '{{csrf_token()}}'
           },
           dataType: 'json',
           success: function(res) {
               var html = "";
               for (var i = 0; i < res.data.length; i++) 
               {
                   var array = res.data[i];

                   var totaResponse = array.total_responses;
                   if(totaResponse==null || totaResponse =='null')
                   {
                     totaResponse = 0;
                   }
                   html += '<tr class="tr" data-id="'+array.encode+'">';
                   html += '<td><span style="cursor:grabbing;"><i class="fa-solid fa-grip-vertical fa-xl"style="color: #c9c9c9;"></i>&nbsp; &nbsp;</span>'+array.sort_order+'</td>';
                   html += '<td style="cursor: pointer;" onclick="window.location.href=\'{{url("/")}}/see-graph-response/'+array.encode+'\'">'+array.title+'</td>';
                   html += '<td>'+array.last_change+'</td>';
                   html += '<td>'+totaResponse+'</td>';

                   var checked = "";
                   if(array.status==1)
                   {
                       var checked = "checked";
                   }
                   var edit = '';
                   if(array.total_responses==0)
                   {
                       edit = '<a href="{{url('/')}}/edit-activity/'+array.encode+'"class="btn  btn-sm" title="{{__("admin.edit")}}"><i class="bx bxs-pencil"></i> {{__("admin.edit")}}</a>';
                   }
                   html += '<td class="d-flexs align-items-center"><div class="row"><div class="col-md-3 col-3"><div class="form-check form-switch" style="display:inline-block;"><input class="form-check-input" type="checkbox"onchange="change_status(\''+array.id+'\')" role="switch" id="switch_'+array.id+'" '+checked+'><label class="form-check-label" for="switch"></label></div></div><div class="col-md-9 col-9"><div class="dropdown"><button class="btn dropdown-toggle" type="button"id="dropdownMenuButton1" data-bs-toggle="dropdown"aria-expanded="false"><i class="fa-solid fa-ellipsis" style="color: #6C757D;"></i></button><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"><li>'+edit+'</li><li> <a href="javascript:void(0)" onclick="delete_activity(\''+array.encode+'\')" class="btn btn-sm"><i class="bx bx-trash" title="{{__("admin.delete")}}"></i> {{__("admin.delete")}}</a></li><li> <a href="javascript:void(0)" onclick="clone_activity(\''+array.encode+'\')" class="btn btn-sm" title="{{__("admin.clone")}}"><i class="bx bxs-copy"></i> {{__("admin.clone")}}</a></li><li><a href="{{url('/')}}/see-response/'+array.encode+'" class="btn  btn-sm" title="{{__("admin.view_response")}}"><i class="bx bx-message-alt-dots"></i> {{__("admin.view_response")}}</a></li></ul></div></div></div></td>';

                   html += '</tr>';
               }
               if(res.data.length==0)
               {
                   $("#sorting-row").html("");
                   html += '<tr><td colspan="5" align="center">{{__("admin.no_data_available")}}</td></tr>';
               }
               $("#sorting-row").html("");
               $("#sorting-row").append(html);
           },
           error: function() {
           }
       });
   }

   let is_stop = 0;
   $(window).on('scroll', function() 
   {
       var sroll = parseInt($(window).scrollTop())+parseInt(window.innerHeight);
       if(sroll > height)
       {
           console.log(height+' - '+sroll);
           if(is_stop==0)
           {
               is_stop++;
               load_activity_date();
           }
       }
   });
   
   $(window).scrollTop();
   
   
</script>
@endpush