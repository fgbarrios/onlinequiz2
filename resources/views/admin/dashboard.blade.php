@extends('layouts.header')
@section('content')
<style type="text/css">
   .status-indicator {
   width: 10px;
   height: 10px;
   border-radius: 50%;
   display: inline-block;
   margin-right: 10px;
   }
   .online {
   background-color: #239f23;
   }
   .offline {
   background-color: #ff6c6c;
   }
   .recent
   {
      margin-bottom: 5px;
      padding: 5px;
   }
   .inviteDriends{
      width: 11rem;height: auto;
   }
   @media only screen and (max-width: 800px) {
      .inviteDriends{
      width: 9rem;
      height: auto;
   }
}
   @media only screen and (max-width: 340px) {
      .inviteDriends{
      width: 9rem;
      height: auto;
   }
}
</style>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="col-lg-12 mb-4 order-0">
         <div class="">
            <div class="d-flex align-items-end row">
               <div class="col-sm-12">
                  <div class="">
                     <div class="row">
                        <div class="col-md-5 col-12 mt-3">
                           <div class="row box-shadow">
                              <div class="col-md-6 col-6" align="left">
                                 <a href="{{route('add-activity')}}" class="btn btn-primary btn-icons"><img class="add-icon" src="{{asset('admin/assets/add_icon.svg')}}">{{__('admin.add_activity')}}</a>
                              </div>
                              <div class="col-md-6 col-6" align="right">
                                 <a href="{{route('list-activity')}}" class="lable_title">{{__('admin.go_to_activities')}}</a>
                              </div>
                           </div>
                           <div class="row box-shadow mt-4">
                        <div class="col-md-12 col-12">
                           <div class="row">
                              <div class="col-md-12 col-12">
                                 <label class="activity_lable">{{__('admin.response_url')}}</label>
                              </div>
                              <div class="col-md-10 col-10" align="left">
                                 <a  class="url_title" href="{{url('/')}}/visit/{{Auth::user()->username}}" target="_blank">{{url('/')}}/visit/{{Auth::user()->username.'...'}} </a>
                              </div>
                              <div class="col-md-2 col-2" align="right">
                                 <a href="{{route('profile')}}" class="lable_title">{{__('admin.edit')}}</a>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-12"><br><br></div>
                              <div class="col-md-12">
                                 <p class="active_status">{{__('admin.activity_status')}} @if($active_count>0)<span class="status-indicator online"></span> @else <span class="status-indicator offline"></span> @endif </p>
                                 @if($active_count>0)
                                 <label><p>{{__('admin.active_status_1')}}</p></label>
                                 @else
                                 <label><p>{{__('admin.active_status_2')}}</p></label>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row box-shadow mt-4 p-0">
                        <div class="col-md-6 col-6 p-0">
                           <img src="{{asset('admin/assets/invite_visitors.png')}}" class="inviteDriends"  align="Invite Visitors">
                        </div>
                        <div class="col-md-6 col-6" align="left">
                           <p class="url_title">{{__('admin.invite_visitors')}}</p>
                           <a href="{{route('invite-visitors')}}" class="btn btn-primary w-100">{{__('admin.get_started')}}</a>
                        </div>
                     </div>
                        </div>
                        <div class="col-md-1 col-1"></div>
                        <div class="col-md-6 col-12 mt-3">
                           <div class="row box-shadow">
                            <div class="col-12">
                            <div class="row">
                              <div class="col-md-12 col-12">
                                 <h5 class="lable_title">{{__('admin.recent_activity')}}</h5>
                              </div>
                           </div>
                           @if(count($activity_data)>0)
                           @foreach($activity_data as $value)
                           <div class="row recent">
                              <p class="activity_list" style="cursor:pointer;" onclick="window.open('{{url('/')}}/see-graph-response/{{app_encode($value->id)}}','_blank');"><i class='bx bxs-notepad' ></i> <label style="cursor:pointer;">{{$value->title}}</label></p>
                           </div>
                           @endforeach
                           @else
                           <div class="row">
                              <div class="col-md-12 col-12">
                                 <label >{{__('admin.no_recent_activity')}}</label>
                              </div>
                           </div>
                           @endif
                            </div>
                           </div>
                        </div>
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
let from    = 101;
let to      = 10;
let until   = 300;

function load_activity_data()
{
   // $.ajax({
   //         url: "{{route('load-activity-data')}}",
   //         type: "GET",
   //         data: {'from' : from,'to' : to},
   //         beforeSend: function() {
   //         },
   //         success: function(data) {
   //             if(data.code==200)
   //             {
   //                from   = to+1;
   //                to     = to+10;
   //                if(to <=until)
   //                {
   //                   load_activity_data();
   //                }
   //                console.log("Done : "+from+" - "+to);
   //             }
   //         },
   //         error: function(e) {
   //         }
   //     });
}

</script>
@endpush