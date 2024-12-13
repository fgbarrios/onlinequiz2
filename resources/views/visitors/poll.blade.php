@php
$message                   = "";
$may_respond_count         = 0;
$may_select_count          = 0;
if(isset($activity_data))
{
$may_respond_count   = ($activity_data->may_respond_count=='')? -1 :$activity_data->may_respond_count;
$may_select_count    = ($activity_data->may_select_count=='')? -1 :$activity_data->may_select_count;
}
if($may_respond_count==config('constant.unlimited_value'))
{
$may_respond_count = -1;
}
if($may_select_count==config('constant.unlimited_value'))
{
$may_select_count = -1;
}
if($may_respond_count==1)
{
$message  = trans('visitors.you_can_respond_once');
}
else if($may_respond_count ==-1 && $may_select_count ==-1)
{
$message  = trans('visitors.you_have_not_responded');
}
else if($may_respond_count >1 && $may_select_count ==-1)
{
$message  = trans('visitors.you_can_respond').' '.$may_respond_count.' '.trans('visitors.times');
}
else if($may_respond_count >1 && $may_select_count >0)
{
$message  = trans('visitors.you_can_respond').' '.$may_respond_count.' '.trans('visitors.times').'. '.trans('visitors.each_option_may_select').' '.$may_select_count.' '.trans('visitors.times');
}
else if($may_respond_count ==-1 && $may_select_count >0)
{
$message  = trans('visitors.you_have_not_responded').'. '.trans('visitors.each_option_may_select').' '.$may_select_count.' '.trans('visitors.times').'.';
}
if($may_respond_count== -1)
{
$may_respond_count = config('constant.unlimited_value');
}
if($may_select_count== -1)
{
$may_select_count = config('constant.unlimited_value');
}
@endphp
<style type="text/css">
   .bar
   {
   background: #cfcfcf;
   border-bottom: 10px solid white;
   }
   .active
   {
   background: #211c4e;
   border-bottom: 10px solid white;
   color: white;
   }
   .box
   {
   background: white;
   text-align: center;
   padding: 8px 20px;
   color: #5c5c5c;
   border-radius: 5px;
   font-weight: 700;
   }
   .pointer
   {
   cursor: pointer;
   }
   .title-image
   {
   display: block;
   width: auto;
   height: auto;
   max-width: 100%;
   max-height: 50vh;
   margin: 0 auto;
   }
   .option-image
   {
   display: block;
   width: auto;
   height: auto;
   max-width: 100%;
   max-height: 50vh;
   margin: 0 auto;
   }
   .del_btn
   {
   display: none;
   }
   @if($may_respond_count==1)
   .score_td
   {
   display : none;
   }
   @endif
   .trash:hover
   {
   color: #853802;
   }
</style>
<div class="row">
   <div class="col-md-12 col-12">
      @isset($activity_data->title)
      <h4>{{$activity_data->title}}</h4>
      <p id="option_alert_message" style="font-weight: 600;">{{$message}}</p>
      @endisset
   </div>
   @if($activity_data->title_image !="")
   <div class="col-md-6 col-6">
      <div class="container">
         <img src="{{asset($activity_data->title_image)}}" align="Title Image" class="title-image">
      </div>
   </div>
   <div class="col-md-6 col-6">
      @else
      <div class="col-md-12 col-12">
         @endif
         @if(isset($response_data))
         <table class="table mt-3" width="100%">
            @foreach($response_data as $value)
            @php
            $active        = '';
            $delete_show   = 'style=display:none;';
            if($value['total_select']>0) 
            {
            $active        = "active";
            $delete_show   = 'style=display:block;';
            }
            @endphp
            <tr class="bar {{$active}}" id="tr_{{$value['option_id']}}">
               <td width="5%" class="pointer score_td" id="count_id_{{$value['option_id']}}" onclick="select_option({{$value['option_id']}})">
                  <div class="box" id="vote_count_{{$value['option_id']}}" >{{$value['total_select']}}</div>
               </td>
               <td width="85%" class="pointer" onclick="select_option({{$value['option_id']}})">@if($value['option_image']!="")<img src="{{asset($value['option_image'])}}" alt="Option Image" class="option-image">@else{{$value['option_text']}}@endif</td>
               <td width="5%" id="del_btn_{{$value['option_id']}}" @if($is_changable==1)onclick="remove_option({{$value['option_id']}})"@endif>
               @if($is_changable==1)
               <label id="delete_vote_{{$value['option_id']}}" class="del_btn" {{$delete_show}}><i class='bx bx-trash trash' style="cursor:pointer;font-size: 2rem;"></i></label>
               @endif
               <input type="hidden" class="available_select" name="available_select_{{$value['option_id']}}" id="available_select_{{$value['option_id']}}" value="{{$value['available_select']}}">
               <input type="hidden" name="current_select_{{$value['option_id']}}" id="current_select_{{$value['option_id']}}" value="{{$value['total_select']}}">
               </td>
            </tr>
            @endforeach
         </table>
         @else
         <table class="table mt-3" width="100%">
            @foreach($option_data as $value)
            <tr class="bar" id="tr_{{$value->id}}">
               <td width="5%" class="pointer score_td" id="count_id_{{$value->id}}" onclick="select_option({{$value->id}})">
                  <div class="box" id="vote_count_{{$value->id}}" >0</div>
               </td>
               <td width="85%" class="pointer" onclick="select_option({{$value->id}})">@if($value->option_image!="")<img src="{{asset($value->option_image)}}" alt="Option Image" class="option-image">@else{{$value->option}}@endif</td>
               <td width="5%" id="del_btn_{{$value->id}}" @if($is_changable==1)onclick="remove_option({{$value->id}})"@endif>
               @if($is_changable==1)
               <label id="delete_vote_{{$value->id}}" class="del_btn"><i class='bx bx-trash trash' style="cursor:pointer;font-size: 2rem;"></i></label>
               @endif
               <input type="hidden" class="available_select" name="available_select_{{$value->id}}" id="available_select_{{$value->id}}" value="{{$may_select_count}}">
               <input type="hidden" name="current_select_{{$value->id}}" id="current_select_{{$value->id}}" value="0">
               </td>
            </tr>
            @endforeach
         </table>
         @endif
      </div>
   </div>
   <div class="col-md-12" align="center">
      <button class="btn btn-primary" type="button" id="submit_btn">{{__('visitors.submit')}}</button>
   </div>
   <div class="col-md-12" align="center" id="response_result_div" style="display:none;">
      <br>
      <div class="alert alert-danger" id="response_result_class" role="alert" align="center">
         <label id="response_result"></label>
      </div>
   </div>
</div>
<!-- Input hiiden values -->
<input type="hidden" name="may_respond_count" id="may_respond_count" value="{{$may_respond_count}}">
<input type="hidden" name="may_select_count" id="may_select_count" value="{{$may_select_count}}">
<input type="hidden" name="total_respond_count" id="total_respond_count" value="{{$activity_data->total_count}}">
<input type="hidden" name="activity_id" id="activity_id" value="{{app_encode($activity_data->id)}}">
<script type="text/javascript">
   is_submit=0;
   $("#submit_btn").click(function(){
      submit_answers();
   });
</script>