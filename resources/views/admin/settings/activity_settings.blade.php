@extends('layouts.header')
@section('content')
<style type="text/css">
 
</style>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-4">
            <h5 class="card-header">{{__('admin.activity_settings')}} {{__('admin.details')}}</h5>
            @if(session('success'))
               <div class="alert alert-success" role="alert">
                  <label>{{session('success')}}</label>
               </div>
            @endif
            @if ($errors->any())
               <div class="alert alert-danger" role="alert">
                  <label>{{$errors->all()[0]}}</label>
               </div>
            @endif
            <!-- Account -->
            <hr class="my-0">
            <div class="card-body">
               <form id="activity_form" action="{{route('save-activity-settings')}}" method="POST" enctype="multipart/form-data" files='true'>
                  @csrf
                  <div class="row">
                     <div class="col-md-4 col-12">
                        <div class="row">
                           <div class="mb-3 col-md-12 col-12">
                              <label for="amount_per_score" class="form-label">{{__('admin.amount_per')}}</label>
                              <div class="input-group">
                                 <input class="form-control" type="number" id="amount_per_score" name="amount_per_score" value="@isset($edit_data){{$edit_data->amount_per_score}}@endisset" {{__('admin.amount_per_score')}}">
                                 <span class="input-group-text">$</span>
                              </div>
                              <label class="invalid-data" id="amount_per_score_error" style="display:none;"></label>
                           </div>
                           <div class="mb-3 col-md-12 col-12">
                              <label for="is_visitor_change_answer" class="form-label">{{__('admin.visitor_can_change')}}</label>
                              <br>
                              <p class="activity_settings"><input type="radio" id="is_visitor_change_answer_1" name="is_visitor_change_answer" value="1" @isset($edit_data) @if($edit_data->is_visitor_change_answer=="1") checked @endif @endisset> {{__('admin.allow_changes')}} &emsp;</p>
                              <p class="activity_settings"><input type="radio" id="is_visitor_change_answer_2" name="is_visitor_change_answer" value="2" @isset($edit_data) @if($edit_data->is_visitor_change_answer=="2") checked @endif @else{{'checked'}}@endisset> {{__('admin.do_not_allow')}} &emsp;</p>
                              <label class="invalid-data" id="is_visitor_change_answer_error" style="display:none;"></label>
                           </div>
                           <div class="mb-3 col-md-12 col-12">
                              <label for="is_text_message" class="form-label">{{__('admin.is_text_message_allow')}}</label>
                              <br>
                              <p class="activity_settings"><input type="radio" id="is_text_message_1" name="is_text_message" value="1" @isset($edit_data) @if($edit_data->is_text_message=="1") checked @endif @endisset> {{__('admin.allow')}} &emsp;</p>
                              <p class="activity_settings"><input type="radio" id="is_text_message_2" name="is_text_message" value="2" @isset($edit_data) @if($edit_data->is_text_message=="2") checked @endif @else{{'checked'}}@endisset> {{__('admin.not_allow')}} &emsp;</p>
                              <label class="invalid-data" id="is_text_message_error" style="display:none;"></label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 col-12">
                        <button class="btn btn-primary" type="submit">{{__('admin.save')}}</button>
                     </div>
                  </div>
                  <input type="hidden" name="id" id="id" value="@isset($edit_data->id){{$edit_data->id}}@else{{'0'}}@endisset">
               </form>
            </div>
            <!-- /Account -->
         </div>
      </div>
   </div>
</div>
@endsection
@push('scripts')

<script type="text/javascript">
   $(document).ready(function(){
      var socket  = new WebSocket('{{config("app.socket_url")}}?user_type=admin&user_id={{auth::user()->id}}');
      var data = {
         type : "poll-check-state"
      };
      socket.onopen = () => socket.send(JSON.stringify(data));
   });
</script>
@endpush