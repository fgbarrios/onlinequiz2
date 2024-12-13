@extends('layouts.header')
@section('content')
<style type="text/css">
 .div-center
   {
      display: flex;
      align-items: center;
   }
</style>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="card">
         <div class="card-body">
            <div class="row">
               <div class="col-md-12 col-12">
                  <button type="button" class="btn btn-primary" onclick="trash_action(1);">{{__('admin.restore')}}</button>
                  <button type="button" class="btn btn-danger" onclick="trash_action(2);">{{__('admin.delete_permenent')}}</button>
               </div>
               <div class="col-md-12 col-12">
                  <br>
                  <div class="alert alert-warning" id="alert_array" style="display: none;" align="center" role="alert">
                     <label id="alert_array_text"></label>
                  </div>
               </div>
            </div>
            <table class="table table-responsive table-hover">
               <thead>
                  <tr>
                     <th width="10%">{{__('admin.sno')}}</th>
                     <th width="70%">{{__('admin.name')}}</th>
                     <th width="20%">{{__('admin.deleted_at')}}</th>
                  </tr>
               </thead>
               <tbody id="sorting-row">
                  @if(count($trash_data)>0)
                  @php $sno = 1; @endphp
                     @foreach($trash_data as $value)
                        <tr>
                           <td><label><input type="checkbox" name="trash_select[]" value="{{app_encode($value->id)}}"> <span>{{$sno}}</span></label></td>
                           <td>{{$value->title}}</td>
                           <td>{{app_date_format($value->deleted_at)}}</td>
                        </tr>
                        @php $sno++; @endphp
                     @endforeach
                  @else
                     <tr>
                        <td colspan="3" align="center">
                           {{__('admin.no_data_available')}}
                        </td>
                     </tr>
                  @endif
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
@endsection
@push('scripts')

<script type="text/javascript">
   
   var socket = new WebSocket('{{config("app.socket_url")}}?user_type=admin&user_id={{auth()->user()->id}}');
   
   // Trash Action either delete or restore
   function trash_action(type) 
   {
      $("#alert_array").hide();
      $("#alert_array_text").text("");
      if(type==1)
      {
         var message = '{{__("admin.restore_alert")}}';
      }
      else
      {
         var message = '{{__("admin.permenent_delete_alert")}}';
      }
      if(confirm(message))
      {
         var select_data = [];
         $("input[name='trash_select[]']:checked").each(function(){
            var value = $(this).val();
            select_data.push(value);
         });
         if(select_data.length==0)
         {
            $("#alert_array").show();
            $("#alert_array_text").text("{{__('admin.trash_alert')}}");
            return false;
         }
         $.ajax({
            url: "{{route('trash-action')}}",
            type: "POST",
            data:  {'type' : type,'data':select_data,'_token' : '{{csrf_token()}}'},
            dataType : 'json',
            success: function(res)
            {
               if(res.code==200)
               {
                  for (var i = 0; i < select_data.length; i++) 
                  {
                     var id = select_data[i];
                     var data = {
                        type: "poll-check-state",
                        visitor_id : id,
                     };
                     socket.send(JSON.stringify(data));
                  }

                  location.reload();
               }
            },     
         });
      }
   }
</script>
@endpush