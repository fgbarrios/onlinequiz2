@extends('layouts.visitors_header')
@section('content')
<!-- Content -->
<style>
   .prasentTiltle{
      font-size: 14px;
    color: #333;
    font-weight: 600;
   }
   .skip_link{
      color:#211C4E;
   }
   .p-5 
      {
         padding: 1rem !important;
      }
   @media (max-width:768px)
   {
      .p-5 
      {
         padding: 0rem !important;
      }
   }
</style>
<div class="" style="margin-top:0px">
   <div class="rounded d-flex justify-content-center">
      <div class="col-md-5 col-sm-12 p-5">
         <div class="text-center">
            <img src="{{asset('admin/assets/logo.svg')}}" alt="Logo" style="">
            <p><br></p>
            <h5 class="mt-3 welcome_title">{{__('visitors.welcome')}}</h5>
            <p style="margin-bottom: 0rem;">{{__('visitors.introduce_yourself')}}</p>
         </div>
         @if ($errors->any())
         <br>
         <div class="alert alert-danger" role="alert" align="center">
            <label>{{$errors->all()[0]}}</label>
         </div>
         @endif
         <form action="{{route('save-visitors-details')}}" method="POST">
            @csrf
            <div class="p-4">
               <div class="row">
                  <div class="mb-3 col-md-12">
                     <label>{{__('visitors.name')}}</label>
                     <input type="text" name="name" id="name" class="form-control" value="">
                  </div>
                  <div class="mb-3 col-md-12">
                     <label>{{__('visitors.email')}}</label>
                     <input type="text" name="email" id="email" class="form-control" value="">
                  </div>
                  <div class="mb-3 col-md-12">
                     <label>{{__('visitors.phone_number')}}</label>
                     <input type="text" name="phone_number" id="phone_number" class="form-control" value="" placeholder="{{__('visitors.with_country_code')}}">
                  </div>
                  <div class="mb-3 col-md-12" align="center">
                     <button type="submit" class="btn btn-primary btn-block">{{__('visitors.continue')}}</button>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript"></script>
@endpush