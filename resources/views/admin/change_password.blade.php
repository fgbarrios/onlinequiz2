@extends('layouts.header')
@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-4">
            <h5 class="card-header">{{__('admin.change_password')}}</h5>
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
            <hr class="my-0">
            <div class="card-body">
               <form method="POST" action="{{ route('change-password') }}">
                  @csrf
                  <div class="row">
                     <div class="mb-3 col-md-6">
                        <label for="old_password" class="form-label">{{__('admin.old_password')}}</label>
                        <input class="form-control" type="password" id="old_password" name="old_password" value="">
                     </div>
                  </div>
                  <div class="row">
                     <div class="mb-3 col-md-6">
                        <label for="new_password" class="form-label">{{__('admin.new_password')}}</label>
                        <input class="form-control" type="password" id="new_password" name="new_password" value="">
                     </div>
                  </div>
                  <div class="row">
                     <div class="mb-3 col-md-6">
                        <label for="confirm_password" class="form-label">{{__('admin.re_enter_password')}}</label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" value="">
                     </div>
                  </div>
                  <div class="mt-2">
                     <button type="submit" class="btn btn-primary me-2">{{__('admin.save')}}</button>
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
<script type="text/javascript"></script>
@endpush