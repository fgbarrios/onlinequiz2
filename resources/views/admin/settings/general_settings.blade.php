@extends('layouts.header')
@section('content')
<style type="text/css">
</style>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-4">
            <h5 class="card-header">{{__('admin.general_settings')}} {{__('admin.details')}}</h5>
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
               <form id="general_form" action="{{route('save-general-settings')}}" method="POST" enctype="multipart/form-data" files='true'>
                  @csrf
                  <div class="row">
                     <div class="mb-3 col-md-6 col-12">
                        <label for="smtp_mailer" class="form-label">{{__('admin.smtp_mailer')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="text" id="smtp_mailer" name="smtp_mailer" value="@isset($edit_data){{$edit_data->smtp_mailer}}@endisset" {{__('admin.smtp_mailer')}}">
                        </div>
                        <label class="invalid-data" id="smtp_mailer_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label for="smtp_host" class="form-label">{{__('admin.smtp_host')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="text" id="smtp_host" name="smtp_host" value="@isset($edit_data){{$edit_data->smtp_host}}@endisset" {{__('admin.smtp_host')}}">
                        </div>
                        <label class="invalid-data" id="smtp_host_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label for="smtp_port" class="form-label">{{__('admin.smtp_port')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="number" id="smtp_port" name="smtp_port" value="@isset($edit_data){{$edit_data->smtp_port}}@endisset" {{__('admin.smtp_port')}}">
                        </div>
                        <label class="invalid-data" id="smtp_username_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label for="smtp_username" class="form-label">{{__('admin.smtp_username')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="text" id="smtp_username" name="smtp_username" value="@isset($edit_data){{$edit_data->smtp_username}}@endisset" {{__('admin.smtp_username')}}">
                        </div>
                        <label class="invalid-data" id="smtp_username_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label for="smtp_password" class="form-label">{{__('admin.smtp_password')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="text" id="smtp_password" name="smtp_password" value="@isset($edit_data){{$edit_data->smtp_password}}@endisset" {{__('admin.smtp_password')}}">
                        </div>
                        <label class="invalid-data" id="smtp_password_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label for="smtp_encryption" class="form-label">{{__('admin.smtp_encryption')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="text" id="smtp_encryption" name="smtp_encryption" value="@isset($edit_data){{$edit_data->smtp_encryption}}@endisset" {{__('admin.smtp_encryption')}}">
                        </div>
                        <label class="invalid-data" id="smtp_encryption_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label for="twilio_secret_key" class="form-label">{{__('admin.twilio_secret_key')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="text" id="twilio_secret_key" name="twilio_secret_key" value="@isset($edit_data){{$edit_data->twilio_secret_key}}@endisset" {{__('admin.twilio_secret_key')}}">
                        </div>
                        <label class="invalid-data" id="twilio_secret_key_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label for="twilio_token" class="form-label">{{__('admin.twilio_token')}}</label>
                        <div class="input-group">
                           <input class="form-control" type="text" id="twilio_token" name="twilio_token" value="@isset($edit_data){{$edit_data->twilio_token}}@endisset" {{__('admin.twilio_token')}}">
                        </div>
                        <label class="invalid-data" id="twilio_token_error" style="display:none;"></label>
                     </div>
                     <div class="mb-3 col-md-6 col-12">
                        <label class="form-label" for="twilio_from_number">{{__('admin.twilio_from_number')}}</label>
                        <div class="group">
                           <span class="input-cuntery">(+1)</span>
                           <input type="number" id="twilio_from_number" name="twilio_from_number" class="form-control country_input" value="@isset($edit_data){{$edit_data->twilio_from_number}}@endisset">
                           <input type="hidden" name="twilio_from_code" id="twilio_from_code" value="1">
                        </div>
                        <label class="invalid-data" id="twilio_from_number_error" style="display:none;"></label>
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
<script type="text/javascript"></script>
@endpush