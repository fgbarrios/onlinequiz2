@extends('layouts.header')
@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-4">
            <h5 class="card-header">{{__('admin.profile')}} {{__('admin.details')}}</h5>
            <!-- Account -->
            <hr class="my-0">
            <div class="card-body">
               <form id="profile_form" action="{{route('update-profile')}}" method="POST">
                  @csrf
                  <div class="row">
                     <div class="mb-3 col-md-6">
                        <label for="username" class="form-label">{{__('admin.username')}}</label>
                        <input class="form-control" type="text" id="username" name="username" value="@isset(auth::user()->username){{auth::user()->username}}@endisset" oninput="change_url();" autofocus="" placeholder="admin9988">
                        @error('username')
                        <label class="invalid-data">{{ $message }}</label>
                        @enderror
                     </div>
                     <div class="mb-3 col-md-6">
                        <label for="polling_url" class="form-label">{{__('admin.url')}}</label>
                        <input class="form-control" type="text" id="polling_url" name="polling_url" value="@isset(auth::user()->polling_url){{auth::user()->polling_url}}@endisset" placeholder="www.onlinepoll.com/admin9988" readonly>
                        @error('polling_url')
                        <label class="invalid-data">{{ $message }}</label>
                        @enderror
                     </div>
                     <div class="mb-3 col-md-6">
                        <label for="email" class="form-label">{{__('admin.email')}}</label>
                        <input class="form-control" type="text" name="email" id="email" value="@isset(auth::user()->email){{auth::user()->email}}@endisset" placeholder="admin@gmail.com">
                        @error('email')
                        <label class="invalid-data">{{ $message }}</label>
                        @enderror
                     </div>
                     <div class="mb-3 col-md-6">
                        <label for="password" class="form-label">{{__('admin.password')}}</label>
                        <input class="form-control" type="password" id="password" name="password" value="">
                     </div>
                     <div class="mb-3 col-md-6">
                        <label for="first_name" class="form-label">{{__('admin.first_name')}}</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="@isset(auth::user()->first_name){{auth::user()->first_name}}@endisset">
                        @error('first_name')
                        <label class="invalid-data">{{ $message }}</label>
                        @enderror
                     </div>
                     <div class="mb-3 col-md-6">
                        <label for="last_name" class="form-label">{{__('admin.last_name')}}</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="@isset(auth::user()->last_name){{auth::user()->last_name}}@endisset">
                        @error('last_name')
                        <label class="invalid-data">{{ $message }}</label>
                        @enderror
                     </div>
                     <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone_number">{{__('admin.phone_number')}}</label>
                        <div class="group">
                           <span class="input-cuntery">(+1)</span>
                           <input type="number" id="phone_number" name="phone_number" class="form-control country_input" value="@isset(auth::user()->phone_number){{auth::user()->phone_number}}@endisset">
                           <input type="hidden" name="phone_code" value="1">
                        </div>
                        @error('phone_number')
                        <label class="invalid-data">{{ $message }}</label>
                        @enderror
                     </div>
                     <div class="mb-3 col-md-6">
                        <label for="time_zone" class="form-label">{{__('admin.time_zone')}}</label>
                        <select id="time_zone" name="time_zone" class="select2 form-select">
                           <option value="">{{__('admin.select_time_zone')}}</option>
                           @foreach($timezones as $value)
                           <option value="{{$value->id}}" @isset(auth::user()->time_zone) @if(auth::user()->time_zone==$value->id) selected @endif @endisset>{{$value->zone_text}}</option>
                           @endforeach
                        </select>
                        @error('time_zone')
                        <label class="invalid-data">{{ $message }}</label>
                        @enderror
                     </div>
                  </div>
                  <div class="mt-2">
                     <button type="submit" class="btn btn-primary me-2">{{__('admin.update')}}</button>
                  </div>
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
   function change_url()
   {
      var username   = $("#username").val();
      var base_url   = '{{url("/")}}';
      var final_url  = base_url+'/visit/'+username;
      $("#polling_url").val(final_url);
   }
</script>
@endpush