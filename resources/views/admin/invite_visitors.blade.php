<!DOCTYPE html>
<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{asset('admin/assets/')}}" data-base-url="{{url('/')}}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
      <title>{{env('APP_NAME')}}</title>
      <meta name="robots" content="noindex, nofollow" />
      <meta name="description" content="" />
      <meta name="keywords" content="">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/fonts/boxicons.css?id=a9a7a946ee13016e04f57799146804c7')}}" />
      <!-- Core CSS -->
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/core.css?id=1a08900f191abf7a8938d25b60d30623')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/theme-default.css?id=3e8cb4751ca766e56a68fe0bd72b5fef')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/css/demo.css?id=6ec63121218f83eed6a13a8aa3decb44')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css?id=858f7088631c9c1fe122f541dcad3a4d')}}" />
      <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/pages/page-auth.css')}}">
      <script src="{{asset('admin/assets/vendor/js/helpers.js')}}"></script>
      <script src="{{asset('admin/assets/js/config.js')}}"></script>
      <script async="async" src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
      <script>
         window.dataLayer = window.dataLayer || [];
         
         function gtag() {
           dataLayer.push(arguments);
         }
         gtag('js', new Date());
         gtag('config', 'GA_MEASUREMENT_ID');
         
      </script>
      <!-- Place this tag in your head or just before your close body tag. -->
      <script async defer src="https://buttons.github.io/buttons.js"></script>
      <style type="text/css">
      .invalid-feedback {
      display: block;
      }
      </style>
   </head>
   <body>
      <!-- Layout Content -->
      <!-- Content -->
      <div class="container-xxl">
         <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
               <!-- Register -->
               <div class="card">
                  <div class="card-body">
                     <h4 class="mb-2" align="center">{{__('admin.invite_visitors')}}</h4>
                     <p class="mb-4" align="center">{{__('admin.enter_email_address_to_invitors')}}</p>
                     @if(session('success'))
                        <div class="alert alert-success" role="alert" align="center">
                           <label>{{session('success')}}</label>
                        </div>
                     @endif
                     @if ($errors->any())
                        <div class="alert alert-danger" role="alert" align="center">
                           <label>{{$errors->all()[0]}}</label>
                        </div>
                     @endif
                     <form method="POST" action="{{route('invite-visitors')}}">
                        @csrf
                        <div class="mb-3">
                           <textarea id="email" class="form-control" name="email" required rows="5" placeholder="{{__('admin.use_comma')}}"></textarea>
                            @error('email')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                        </div>
                        <div class="mb-3">
                           <textarea id="message" class="form-control" name="message" rows="5" placeholder="{{__('admin.add_message_optional')}}"></textarea>
                           @error('message')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                        </div>
                        <div class="mb-3" align="center">
                           <button class="btn btn-primary d-grid w-100" type="submit">{{__('admin.send_invitation')}}</button>
                           <br>
                           <a href="{{route('dashboard')}}">{{__('admin.cancel')}}</a>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
      </div>
      <script src="{{asset('admin/assets/vendor/libs/jquery/jquery.js?id=61b6b590f1cc3c668eb64b481bd6524c')}}"></script>
      <script src="{{asset('admin/assets/vendor/libs/popper/popper.js?id=ce797c28ba57ea09097033fefdb24ace')}}"></script>
      <script src="{{asset('admin/assets/vendor/js/bootstrap.js?id=321ba18461117b7054cc5b6de1640b8d')}}"></script>
      <script src="{{asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js?id=302c26f0ffdcc86cc83a6bb8ec2164ad')}}"></script>
      <script src="{{asset('admin/assets/vendor/js/menu.js?id=6b677e3837c846fa942e7f738748f228')}}"></script>
      <script src="{{asset('admin/assets/js/main.js?id=5971037ce98c7a1fb1c8931365d154ea')}}"></script>
      <script type="text/javascript">
          setTimeout(function(){
            $(".alert").hide();
         },4000);
      </script>
   </body>
</html>