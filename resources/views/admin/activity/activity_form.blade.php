@extends('layouts.header')
@section('content')
<style type="text/css">
   .option {
   background: #fff;
   padding: 10px 10px;
   border: 1px solid #C8C8C8;
   border-radius: 11px;
   }
   .fs-2 {
   font-size: 2rem;
   }
   .update_img {
   display: flex;
   gap: 6px;
   }
   .div-center {
   display: flex;
   align-items: center;
   justify-content: space-between;
   gap:13px
   }
   .correct-answer {
   background: green;
   color: white;
   }
   .Count-div {
   width: 130px;
   }
   .score_input{
   text-align:center;
   }
   .center_div {
   width: 100%;
   }
   .border-right{
   border-right: none;
   }
   .active_btns {
   margin: 15px 0px;
   }
   .accordion-flush {
   border:none !important;
   }
   .accordion-button{
   background-color: #F7F7F7;
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
   .accordion-button:not(.collapsed) {
   color: #333;
   background-color: #fff;
   box-shadow: inset 0 0 0 #d9dee3;
   }
   .accordion-button {
   color: #333;
   }
   .accordion-flush {
   border-bottom: 1px solid #a5a5a5;
   border-radius: 0px;
   margin-bottom: 10px;
   }
   .align-justy {
   height: 100%;
   display: flex;
   flex-direction: column;
   justify-content: space-between;
   }
   .option_name
   {
   margin: 0;
   padding: 1px 10px;
   font-size: 20px;
   display: none;
   }
   .container-xxl, .container-xl, .container-lg, .container-md, .container-sm, .container 
   {
   max-width: 100%;
   }
   .bg
   {
      background: #ededed;
      padding: 0.5rem;
   }
   .form-control
   {
      border-radius: 0.375rem !important;
   }
</style>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
<div class="row">
   <div class="col-md-12">
      <div class="card mb-4">
         <h5 class="card-header">{{__('admin.activity')}} {{__('admin.details')}}</h5>
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
            <form id="activity_form" action="{{route('save-activity')}}" method="POST"
               enctype="multipart/form-data" files='true'>
               @csrf
               <div class="row">
                  <div class="col-md-9 col-12">
                     <div class="row">
                        <div class="mb-3 col-md-12 col-12">
                           <label for="title" class="form-label">{{__('admin.title')}}</label>
                           <div class="input-group">
                              <input class="form-control border-right" type="text" id="title" name="title"
                                 value="@isset($edit_data){{$edit_data[0]->title}}@endisset"
                                 placeholder="{{__('admin.enter')}} {{__('admin.title')}}"
                                 maxlength="1000">
                              @php
                              $path = "";
                              $style = "display:none;";
                              @endphp
                              @isset($edit_data[0]->title_image)
                              @php
                              if($edit_data[0]->title_image !="")
                              {
                              $path = asset($edit_data[0]->title_image);
                              $style = "display:block;";
                              }
                              @endphp
                              @endisset
                              @if($path)
                              <span class="input-group-text" style="{{$style}}"
                                 id="title_preview_image"><a href="{{$path}}" target="_blank"><img
                                 src="{{$path}}" id="title_preview"
                                 style="height: auto;width: 40px;" alt="Title Image"></a></span>
                              @endif
                              <span class="input-group-text" id="upload_title_image"
                              style="cursor:pointer;"
                              onclick="upload_title_image(@if($path==""){{'1'}}@else{{'2'}}@endif)"
                              title="Upload Image">@if($path=="")<i class='bx bx-image fs-2'></i>@else
                              <i class="bx bx-trash fs-2"></i> @endif</span>
                              <input type="file" accept="image/png, image/jpg, image/jpeg, image/webp"
                                 style="display:none;" name="title_file" id="title_file">
                           </div>
                           <label class="invalid-data" id="title_error" style="display:none;"></label>
                        </div>
                     </div>
                     <div class="row" id="option_list">
                     </div>
                     <div class="row">
                        <div class="active_btns px-2">
                           <button class="btn btn-primary" id="add_more_options" type="button" onclick="add_options()">{{__('admin.add')}} {{__('admin.option')}}</button>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-12">
                     <div class="align-justys">
                        @isset($edit_data[0])
                        <div class="accordion accordion-flush m-0" id="accordion_1">
                           <div class="accordion-item">
                              <h2 class="accordion-header" id="flush-accordion_1">
                                 <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                    aria-expanded="false" aria-controls="flush-collapseOne">
                                 {{__('admin.response_settings')}}
                                 </button>
                              </h2>
                              @php
                              $response_type = explode(',',$edit_data[0]->response_type);
                              //print_r($response_type);
                              @endphp
                              <div id="flush-collapseOne" class="accordion-collapse collapse"
                                 aria-labelledby="flush-accordion_1" data-bs-parent="#accordion_1">
                                 <div class="accordion-body">
                                    <div class="row">
                                       <div class="col-md-12 col-12">
                                          <p><input type="checkbox" id="response_type_1"
                                             name="response_type[]" value="1" @if(in_array('1',$response_type)) checked @elseif($response_type[0]=="") checked @endif>
                                             {{__('admin.website')}}
                                          </p>
                                          <p>{{__('admin.webshare_content_1')}}<a href="{{Auth::user()->polling_url}}" target="_blank">{{Auth::user()->username}}</a>{{__('admin.webshare_content_2')}}</p>
                                       </div>
                                       @isset($activity_data->is_text_message) @if($activity_data->is_text_message=='1')
                                          <div class="col-md-12 col-12" id="response_type_div" @isset($edit_data[0]->is_multiple_type)@if($edit_data[0]->is_multiple_type=='2'){{'style="display:block;"'}}@else{{'style="display:none;"'}}@endif@else{{'style="display:block;"'}}@endisset>
                                             <p> <input type="checkbox" id="response_type_2" name="response_type[]" onclick="toggle_text_message()" value="2" @if(in_array('2',$response_type)) checked @endif>
                                                {{__('admin.text_message')}}
                                             </p>
                                             <p class="text_content" style="display: none;">{{__('admin.textshare_content_1')}} <a href="{{Auth::user()->polling_url}}" target="_blank">{{Auth::user()->username}}</a> to {{$sms_number}} {{__('admin.textshare_content_2')}} 
                                             </p>
                                             <select id="option_text_type" name="option_text_type" class="text_content" onchange="set_options_name()" style="display: none;">
                                             <option value="1" @isset($edit_data[0]->option_text_type) @if($edit_data[0]->option_text_type=='1'){{'selected'}}@endif @endisset>ABC</option>
                                             <option value="2" @isset($edit_data[0]->option_text_type) @if($edit_data[0]->option_text_type=='2'){{'selected'}}@endif @endisset>123</option>
                                             </select>
                                             @endif
                                          </div>
                                       @endisset
                                       <div class="col-md-12 col-12">
                                          <label class="invalid-data" id="response_type_error"
                                             style="display:none;"></label>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endisset
                        <div class="accordion accordion-flush m-0" id="accordion_2">
                           <div class="accordion-item active">
                              <h2 class="accordion-header" id="flush-accordion_2">
                                 <button class="accordion-button" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                                    aria-expanded="true" aria-controls="flush-collapseTwo">
                                 {{__('admin.how_people_can_respond')}}
                                 </button>
                              </h2>
                              <div id="flush-collapseTwo" class="accordion-collapse collapse show"
                                 aria-labelledby="flush-accordion_2" data-bs-parent="#accordion_2">
                                 <div class="accordion-body">
                                    <div class="row">
                                       @isset($edit_data[0])
                                       <div class="mb-3 col-md-12 col-12 resClass" @if($edit_data[0]->is_multiple_type=='2'){{'style="display:block"'}}@else{{'style="display:none"'}}@endif>
                                          <label for="is_had_score"
                                             class="form-label">{{__('admin.each_person_may_respond')}}</label>
                                          <br>
                                          <p><input type="radio" id="may_respond_1"
                                             onchange="chagne_may_respond_value();"
                                             name="may_respond" value="1"
                                             @if($edit_data[0]->may_respond=="" ||
                                             $edit_data[0]->may_respond=="1") checked @endif>
                                             {{__('admin.upto')}}&emsp;<input type="number"
                                             id="may_respond_count" class="form-control"
                                             name="may_respond_count"
                                             value="@if($edit_data[0]->may_respond_count==""){{'1'}}@else{{$edit_data[0]->may_respond_count}}@endif"
                                             style="display: inline-block;width: 30%;">&emsp;{{__('admin.times')}}
                                          </p>
                                          <p><input type="radio" id="may_respond_2"
                                             onchange="chagne_may_respond_value();"
                                             name="may_respond" value="2"
                                             @if($edit_data[0]->may_respond=="2") checked @endif>
                                             {{__('admin.unlimited')}} &emsp;
                                          </p>
                                          <label class="invalid-data" id="may_respond_error"
                                             style="display:none;"></label>
                                       </div>
                                       <div class="mb-3 col-md-12 col-12 resClass" @if($edit_data[0]->is_multiple_type=='2'){{'style="display:block"'}}@else{{'style="display:none"'}}@endif>
                                          <label for="is_had_score"
                                             class="form-label">{{__('admin.each_option_may_select')}}</label>
                                          <br>
                                          <p><input type="radio" id="may_select_1"
                                             onchange="chagne_may_select_value();" name="may_select"
                                             value="1" @if($edit_data[0]->may_select=="" ||
                                             $edit_data[0]->may_select=="1") checked @endif>
                                             {{__('admin.upto')}} &emsp;<input type="number"
                                             id="may_select_count" class="form-control"
                                             name="may_select_count"
                                             value="@if($edit_data[0]->may_select_count==""){{'1'}}@else{{$edit_data[0]->may_select_count}}@endif"
                                             style="display: inline-block;width: 30%;">&emsp;
                                             {{__('admin.times')}} 
                                          </p>
                                          <p><input type="radio" id="may_select_2"
                                             onchange="chagne_may_select_value();" name="may_select"
                                             value="2" @if($edit_data[0]->may_select=="2") checked
                                             @endif> {{__('admin.unlimited')}} &emsp;
                                          </p>
                                          <label class="invalid-data" id="may_select_error"
                                             style="display:none;"></label>
                                       </div>
                                       @endisset
                                       <div class="mb-3 col-md-12 col-12">
                                          <label for="is_had_score"
                                             class="form-label">{{__('admin.is_score_type_option')}}</label>
                                          <br>
                                          <p><input type="radio" id="is_had_score_1"
                                             onchange="is_score_type();" name="is_had_score"
                                             value="1" @isset($edit_data)
                                             @if($edit_data[0]->is_had_score=="1") checked @endif
                                             @endisset> {{__('admin.yes')}} &emsp;
                                          </p>
                                          <p><input type="radio" id="is_had_score_2"
                                             onchange="is_score_type();" name="is_had_score"
                                             value="2" @isset($edit_data)
                                             @if($edit_data[0]->is_had_score=="2") checked @endif
                                             @else{{'checked'}}@endisset> {{__('admin.no')}} &emsp;
                                          </p>
                                          <label class="invalid-data" id="is_had_score_error"
                                             style="display:none;"></label>
                                       </div>
                                       <div class="mb-3 col-md-12 col-12">
                                          <label for="is_had_score"
                                             class="form-label">{{__('admin.is_multi_option_type')}}</label>
                                          <br>
                                          <p><input type="radio" id="is_multiple_type_1"
                                             onchange="is_multiple_type_div();" name="is_multiple_type"
                                             value="1" @isset($edit_data)
                                             @if($edit_data[0]->is_multiple_type=="1") checked @endif
                                             @endisset> {{__('admin.yes')}} &emsp;
                                          </p>
                                          <p><input type="radio" id="is_multiple_type_2"
                                             onchange="is_multiple_type_div();" name="is_multiple_type"
                                             value="2" @isset($edit_data)
                                             @if($edit_data[0]->is_multiple_type=="2") checked @endif
                                             @else{{'checked'}}@endisset> {{__('admin.no')}} &emsp;
                                          </p>
                                          <label class="invalid-data" id="is_multiple_type_error"
                                             style="display:none;"></label>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @isset($edit_data[0])
                        <div class="accordion accordion-flush m-0" id="accordion_3">
                           <div class="accordion-item">
                              <h3 class="accordion-header" id="flush-accordion_3">
                                 <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseThree"
                                    aria-expanded="false" aria-controls="flush-collapseThree">
                                 {{__('admin.share_and_embed')}}
                                 </button>
                              </h3>
                              <div id="flush-collapseThree" class="accordion-collapse collapse"
                                 aria-labelledby="flush-accordion_3" data-bs-parent="#accordion_3">
                                 <div class="accordion-body">
                                    <div class="row">
                                       <div class="mb-3 col-md-12 col-12">
                                          <label class="form-label">{{__('admin.response_link')}}</label>
                                          <p>{{__('admin.response_para')}}</p>
                                          <a href="javascript:void(0)" onclick="copy_url('{{url("/")}}/visit/{{Auth::user()->username}}')" id="copy_url">{{__('admin.copy_response_link')}}</a>
                                       </div>
                                       <div class="mb-3 col-md-12 col-12">
                                          <label class="form-label">{{__('admin.share_qr_code')}}</label>
                                          <br><br>
                                          {{QrCode::size(150)->generate(url("/").'/visit/'.Auth::user()->username)}}
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endisset
                     </div>
                  </div>
                  <!-- <br> -->
                  <div class="row">
                     <div class="col-md-12 col-12" align="center">
                        <label class="invalid-data" id="final_error" style="display:none;"></label>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 col-6">
                        
                     </div>
                     <div class="col-md-6 col-6" align="right">
                        <div class="active_btns px-2">
                           @isset($id)
                           <button class="btn btn-primary" type="button"
                              onclick="form_validation(1)">{{__('admin.update')}}</button>
                           @else
                           <button class="btn btn-primary" type="button"
                              onclick="form_validation(1)">{{__('admin.create')}}</button>
                           @endisset
                           <button class="btn btn-primary" type="button"
                              onclick="form_validation(2)">{{__('admin.add_another_activity')}}</button>
                        </div>
                     </div>
                  </div>
                  <!-- Hidden Fields -->
                  <input type="hidden" name="activity_id" id="activity_id"
                     value="@isset($id){{$id}}@else{{'0'}}@endisset">
                  <input type="hidden" name="title_type" id="title_type"
                     value="@isset($edit_data){{$edit_data[0]->title_type}}@else{{'1'}}@endisset">
                  <input type="hidden" name="total_options" id="total_options" value="1">
                  <input type="hidden" name="submit_type" id="submit_type" value="1">
                  <input type="hidden" name="is_delete_title_image" id="is_delete_title_image" value="0">
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
   // Assign options during edit page
   
   @isset($edit_data)
      @if($edit_data[0]->is_multiple_type=="1")
         let multi_options = <?php echo json_encode($edit_data_options); ?>;
         let options       = [];
      @else
         let options       = <?php echo json_encode($edit_data_options); ?>;
         let multi_options = [];
      @endif
   @else
      let options       = [];
      let multi_options = [];
   @endisset
   
   $(document).ready(function() {

       chagne_may_respond_value();
       chagne_may_select_value();
   
       $("#title_file").change(function() {
           const file = this.files[0];
           if (file) {
               let reader = new FileReader();
               reader.onload = function(event) {
                   $('#title_preview').attr('src', event.target.result);
                   $("#title_preview_image").show();
                   $("#is_delete_title_image").val("0");
               }
               reader.readAsDataURL(file);
               $("#upload_title_image").attr("onclick", "upload_title_image(2)");
               $("#upload_title_image").html('<i class="bx bx-trash fs-2" style="cursor:pointer;"></i>');
               $("#upload_title_image").attr("onclick", "upload_title_image(2)");
               $("#upload_title_image").attr("title", "Delete Uploaded Image");
           }
       });
       is_multiple_type_div(0);
   });
   
   // Title image upload
   
   function upload_title_image(type) {
       if (type == 1) {
           $("#title_file").click();
       } else {
           $("#title_type").val(1);
           $("#title_file").val("");
           $("#upload_title_image").attr("onclick", "upload_title_image(1)");
           $("#upload_title_image").html('<i class="bx bx-image fs-2"></i>');
           $("#upload_title_image").attr("onclick", "upload_title_image(1)");
           $("#upload_title_image").attr("title", "Upload Image");
           $('#title_preview').attr('src', "");
           $("#title_preview_image").hide();
           $("#is_delete_title_image").val("1");
       }
   }
   
   // Initiat options sortable
   
   function initial_sortable() {
       $("#option_list").sortable({
           placeholder: "sortable-placeholder",
       });
       $("#option_list").disableSelection();
   }
   
   // Score type based options
   
   function is_score_type() {
       var type = $("input[name='is_had_score']:checked").val();
       if (type == "1") {
           $(".score_div").show();
       } else {
           $(".score_div").hide();
           $(".score_input").each(function() {
               $(this).val("");
           });
       }
   }
   
   // Check correct option
   
   function make_correct(row) {
       var value = $("#is_correct_" + row).val();
       if (value == "1" || value == 1) {
           $("#is_correct_" + row).val("2");
           $("#correct_div_" + row).attr("class", "");
       } else {
           $("#is_correct_" + row).val("1");
           $("#correct_div_" + row).attr("class", "correct-answer");
       }
   }
   
   // Option image upload
   
   function upload_option_image(row) {
       $("#option_file_" + row).click();
       $("#option_file_" + row).change(function() {
           const file = this.files[0];
           if (file) {
               upload_option_image_temp(file, row);
               $("#option_file_" + row).val("");
           }
       });
   }
   
   // Delete option uploaded image
   
      function delete_uploaded_option_image(row)
      {
         $('#preview_option_image_'+row).attr('src', "");
         $("#option_file_"+row).val("");
         $("#option_text_"+row).prop("disabled",false);
         $("#preview_img_"+row).hide();
         $("#option_file_url_"+row).val("");
      }
   
   // Prepare Options
   
   function load_options(is_add = 0) {
       var html = '';
       for (var i = 0; i < options.length; i++) {
           var array = options[i];
           var option_text = array.option_text;
           var option_disabled = "";
           var preview_display = 'none';
           var correct_answer = '';
           var file = array.image_url;
           var asset = '{{asset("")}}';
   
           var src = "";
           if (file) {
               $('#preview_option_image_' + i).attr('src', asset + file);
               preview_display = 'block';
               option_text = "";
               option_disabled = "disabled";
               src = asset + '/' + file;
           }
           if (array.is_correct == "1") {
               correct_answer = 'correct-answer';
           }
   
           if (file == undefined || file == 'undefined') file = "";
   
           html +=
               '<div class="col-12 col-md-12 mb-3"><div class="option div-center"><input type="hidden" name="sord_order_' +
               i + '" id="sord_order_' + i + '" value="' + array.sort_order + '"><input type="hidden" name="option_id_' +
               i + '" id="option_id_' + i + '" value="' + array.option_id +
               '"><div class="dragbutton" style="cursor:grabbing; display:flex"><span><i class="bx bxs-grid fs-2"></i></span>&emsp;<span id="correct_div_' +
               i + '" onclick="make_correct(' + i + ')" class="' + correct_answer +
               '"><i class="bx bx-check fs-2"></i></span>&emsp;<input type="hidden" name="is_correct_' + i +
               '" id="is_correct_' + i + '" class="form-control" value="' + array.is_correct +
               '"></div><div class="Count-div score_div" style="display:none"><input type="number" min="0" name="score_' + i +
               '" id="score_' + i + '" class="form-control score_input" value="' + array.score +
               '"></div><div class="center_div"><input type="text" name="option_text_' + i +
               '" id="option_text_' + i + '" class="form-control" placeholder="Text, Image" maxlength ="1000" ' +
               option_disabled + ' value="' + option_text +
               '"></div><div class="update_img" align="right"><span id="option_image_' + i +
               '" onclick="upload_option_image(' + i +
               ')"><i class="bx bx-image fs-2" style="cursor:pointer;"></i></span><span id="option_delete_' + i +
               '" onclick="delete_option(\'' + i +
               '\')"><i class="bx bx-trash fs-2" style="cursor:pointer;"></i></span><span class="option_name" id="option_name_'+i+'">'+array.option_name+'</span><input type="hidden" name="option_name_value_'+i+'" id="option_name_value_'+i+'" value="'+array.option_name+'"><input type="file" accept="image/png, image/jpg, image/jpeg, image/webp" style="display:none;" name="option_file_' +
               i + '" id="option_file_' + i + '"><input type="hidden" name="option_file_url_' + i +
               '" id="option_file_url_' + i + '" value="' + file +
               '"></div></div><div class="img_div" id="preview_img_' + i + '" style="display:' +
               preview_display +
               ';"><div class="row"><div class="col-md-12 col-lg-12 col-xl-12" align="center"><img src="' + src +
               '" id="preview_option_image_' + i +
               '" class="option-img" alt="Option Image"></div><div class="col-md-12 col-lg-12 col-xl-12" align="center"><button class="btn btn-danger btn-lg btn-block" type="button" onclick="delete_uploaded_option_image(' +
               i +
               ')">Delete Image</button></div></div></div></div><div class="col-md-12 col-lg-12 col-xl-12" align="center"><label class="invalid-data row_error" id="row_error_' +
               i + '" style="display:none;"></label></div></div>';
       }
       if (is_add == 1 || options.length == 0) {
           html +=
               '<div class="col-12 col-md-12 mb-3"><div class=" option div-center"><input type="hidden" name="sord_order_' +
               i + '" id="sord_order_' + i +
               '" value=""><div class="dragbutton" style="cursor:grabbing; display:flex"><span><i class="bx bxs-grid fs-2"></i></span>&emsp;<span id="correct_div_' +
               i + '" onclick="make_correct(' + i +
               ')"><i class="bx bx-check fs-2"></i></span>&emsp;<input type="hidden" name="is_correct_' + i +
               '" id="is_correct_' + i +
               '" class="form-control" value="2"></div><div class="Count-div score_div" style="display:none"><input type="number" min="0" name="score_' +
               i + '" id="score_' + i +
               '" class="form-control score_input" value=""></div><div class="center_div"><input type="text" name="option_text_' +
               i + '" id="option_text_' + i +
               '" class="form-control" placeholder="Text, Image" maxlength ="1000" value=""></div><div class="update_img" align="right"><span id="option_image_' +
               i + '" onclick="upload_option_image(' + i +
               ')"><i class="bx bx-image fs-2" style="cursor:pointer;"></i></span><span class="option_name" id="option_name_'+i+'"></span><input type="hidden" name="option_name_value_'+i+'" id="option_name_value_'+i+'" value=""><input type="file" accept="image/png, image/jpg, image/jpeg, image/webp" style="display:none;" name="option_file_' +
               i + '" id="option_file_' + i + '"><input type="hidden" name="option_file_url_' + i +
               '" id="option_file_url_' + i +
               '" value=""></div></div><div class="img_div" id="preview_img_' + i +
               '" style="display:none;"><div class="row"><div class="col-md-12 col-lg-12 col-xl-12" align="center"><img src="" id="preview_option_image_' +
               i +
               '" class="option-img" alt="Option Image"></div><div class="col-md-12 col-lg-12 col-xl-12" align="center"><button class="btn btn-danger btn-lg btn-block" type="button" onclick="delete_uploaded_option_image(' +
               i +
               ')">Delete Image</button></div></div></div></div><div class="col-md-12 col-lg-12 col-xl-12" align="center"><label class="invalid-data row_error" id="row_error_' +
               i + '" style="display:none;"></label></div>';
       }
   
       $("#total_options").val(i + 1);
       $("#option_list").html(html);
       initial_sortable();
       is_score_type();
       toggle_text_message(1);
       set_options_name();
   }
   
   function is_multiple_type_div(val)
   {
      var type = $('input[name="is_multiple_type"]:checked').val();
      $("#total_options").val(0);
      $("#option_list").html("");
      var res_array = '@isset($edit_data[0]->response_type){{$edit_data[0]->response_type}}@endisset';
      $("#response_type_2").prop("checked",false);
      if(type==1)
      {
         $("#response_type_div").hide();
         $(".resClass").hide();
         load_multiple_options(val);
      }
      else
      {
         var array = res_array.split(',');
         if(array.includes('2'))
         {
            $("#response_type_2").prop("checked",true);
         }
         $("#response_type_div").show();
         $(".resClass").show();
         load_options(val);
      }
   }

   function load_multiple_options(is_add)
   {
       var html = '';
       for (var i = 0; i < multi_options.length; i++) 
       {
           var array          = multi_options[i];
           var question       = array.question;
           var sort_order     = array.sort_order;
           var options_array  = array.options_array;

           var sub_html = "";
           sub_html     += '<div class="col-md-4 col-10"><div class="row">';
           for (var j = 0; j < options_array.length; j++) 
           {
              var sub_array = options_array[j];
              sub_html     += '<div class="col-md-12 col-12 mt-1"><div class="input-group"><input type="number" min="0" name="score_'+i+'_'+sub_array.options_no+'" id="score_'+i+'_'+sub_array.options_no+'" style="margin-right: 0.3rem;display: none;" class="form-control score_input score_div" value="'+sub_array.score+'" placeholder="{{__('admin.score')}}"><input type="text" class="form-control options_'+i+'" name="option_'+i+'_'+sub_array.options_no+'" id="option_'+i+'_'+sub_array.options_no+'" value="'+sub_array.options+'" style="width: 55%;"></div></div>';
           }
           sub_html     += '</div></div><div class="col-md-1 col-2"><br><span id="option_delete_' + i +
               '" onclick="delete_multi_option(\'' + i +
               '\')"><i class="bx bx-trash fs-2" style="cursor:pointer;"></i></span></div>';

           html += '<div class="col-md-12"><div class="row mt-1 bg center_div"><div class="col-md-7 col-12"><br><div class="input-group"><span><i class="bx bxs-grid fs-2"></i></span>&emsp;<input type="hidden" name="sord_order_'+i+'" id="sord_order_'+i+'" value="'+sort_order+'"><input type="text" class="form-control options" name="question_'+i+'" id="question_'+i+'" value="'+question+'"></div></div>'+sub_html+'<div class="col-md-12" align="center"><label class="invalid-data row_error" id="row_error_' +i+ '" style="display:none;"></label></div></div></div>';
       }
       if (is_add == 1 || multi_options.length == 0) {
           html +='<div class="col-md-12"><div class="row mt-1 bg center_div"><div class="col-md-7 col-12"><br><div class="input-group"><span><i class="bx bxs-grid fs-2"></i></span>&emsp;<input type="hidden" name="sord_order_'+i+'" id="sord_order_'+i+'" value=""><input type="text" class="form-control options" name="question_'+i+'" id="question_'+i+'" value=""></div></div><div class="col-md-5 col-12"><div class="row"><div class="col-md-12 col-12 mt-1"><div class="input-group"><input type="number" min="0" name="score_'+i+'_1" id="score_'+i+'_1" style="margin-right: 0.3rem;display: none;" class="form-control score_input score_div" value="" placeholder="{{__('admin.score')}}"><input type="text" class="form-control options_'+i+'" name="option_'+i+'_1" id="option_'+i+'_1" value="" style="width: 55%;"></div></div><div class="col-md-12 col-12 mt-1"><div class="input-group"><input type="number" min="0" name="score_'+i+'_2" id="score_'+i+'_2" style="margin-right: 0.3rem;display: none;" class="form-control score_input score_div" value="" placeholder="{{__('admin.score')}}"><input type="text" class="form-control options_'+i+'" name="option_'+i+'_2" id="option_'+i+'_2" value="" style="width: 55%;"></div></div></div></div><div class="col-md-12" align="center"><label class="invalid-data row_error" id="row_error_' +i+ '" style="display:none;"></label></div></div></div>';
       }
   
       $("#total_options").val(i + 1);
       var length = multi_options.length;
       if(is_add==1)
       {
         length++;
       }
       if(length==0)
       {
         length++;
       }
       $("#may_respond_count").val(length);
       $("#option_list").html(html);
       initial_sortable();
       is_score_type();
       toggle_text_message(1);
       set_options_name();
   }

   // Add options
   
   function add_options() {
       var temp   = {};
       var type   = $('input[name="is_multiple_type"]:checked').val();

       if(type==1)
       {
         var row               = multi_options.length;
         if ($("#question_" + row).length > 0) 
         {
            var score             = $("#score_" + row).val();
            temp['row']           = row;
            temp['question']      = $("#question_" + row).val();
            temp['sort_order']    = "";

            var options_array     = [];
            var opt_no            = 1;

            $(".options_"+row).each(function()
            {

               var temp1               = {};
               temp1['options_no']     = opt_no;
               temp1['options']        = $("#option_"+row+"_"+opt_no).val();
               temp1['score']          = $("#score_" + row+"_"+opt_no).val();
               temp1['option_id']      = "";
               temp1['option_name']    = "";
               options_array.push(temp1);
               opt_no++;

            });

            temp['options_array'] = options_array;
            multi_options.push(temp);
         }
         load_multiple_options(1);
       }
       else
       {
         var row    = options.length;
          if ($("#is_correct_" + row).length > 0) 
          {
              var is_correct        = $("#is_correct_" + row).val();
              var image_url         = $("#option_file_" + row)[0].files[0];
              var option_text       = $("#option_text_" + row).val();
              var score             = $("#score_" + row).val();
              temp['row']           = row;
              temp['is_correct']    = is_correct;
              temp['image_url']     = image_url;
              temp['option_text']   = option_text;
              temp['score']         = score;
              temp['option_id']     = "";
              temp['option_name']   = "";
              temp['sort_order']    = "";
              options.push(temp);
          }
          load_options(1);
       }
   }
   
   // Delete options
   
   function delete_option(index) {
       options.splice(index, 1);
       load_options();
   }

   function delete_multi_option(index) {
       multi_options.splice(index, 1);
       load_multiple_options();
   }
   
   // Form validation
   
   function form_validation(type = 1) {
   
       var title              = $("#title").val();
       var title_file         = $("#title_file")[0].files.length;
       var is_had_score       = $('input[name="is_had_score"]:checked').val();
       var is_score_empty     = 0;
       var is_option_empty    = 0;
       var total_error_count  = 0;
   
      $("#title_error").hide();
      $("#is_had_score_error").hide();
      $("#final_error").hide();
      $("#title_error").text("");
      $("#is_had_score_error").text("");
      $("#may_respond_error").text("");
      $("#may_respond_error").hide();
      $("#may_select_error").text("");
      $("#may_select_error").hide();
      $(".row_error").hide();

         if(title=="")
         {
            $("#title_error").show();
            $("#title_error").text("{{__('admin.activity_title_error')}}");
            total_error_count++;
            return false;
         }
   
         if(is_had_score !="1" && is_had_score !="2")
         {
            $("#is_had_score_error").show();
            $("#is_had_score_error").text("{{__('admin.activity_is_score_type_error')}}");
            total_error_count++;
            return false;
         }
   
         var may_select    = $("input[name='may_select']:checked").val();
         var may_respond   = $("input[name='may_respond']:checked").val();
         var is_multiple   = $('input[name="is_multiple_type"]:checked').val();
   
         if(may_select !="undefined" || may_select != undefined)
         {
            var may_select_count = $("#may_select_count").val();
            if(may_select=="1" || may_select==1)
            {
               if(may_select_count <=0 || may_select_count == "")
               {
                  $("#may_select_error").text('{{__("admin.may_select_error")}}');
                  $("#may_select_error").show();
                  total_error_count++;
                  return false;
               }
            }
         }
   
         if(may_respond !="undefined" || may_respond != undefined)
         {
            var may_respond_count = $("#may_respond_count").val();
            if(may_respond=="1" || may_respond==1)
            {
               if(may_respond_count <=0 || may_respond_count == "")
               {
                  $("#may_respond_error").text('{{__("admin.may_respond_error")}}');
                  $("#may_respond_error").show();
                  total_error_count++;
                  return false;
               }
            }
         }
   
         var sort_order = 1;
         $(".score_input").each(function(){
            if(is_multiple==1)
            {
               var rnno = 1;
               $(".options").each(function(){
                  var id         = $(this).attr("name").split("_")[1];
                  $("#sord_order_" + id).val(rnno);
                  rnno++;
                  var score_error = 0;
                  $(".options_"+id).each(function(){
                    var subno = $(this).attr("name").split("_")[2];
                    if($(this).val()=="")
                    {
                        $("#row_error_"+id).text("{{__('admin.row_option_error')}}");
                        $("#row_error_"+id).show();
                        total_error_count++;
                        return false;
                    }
                    if(is_had_score=="1")
                    {
                        if($("#score_"+id+"_"+subno).val()=="")
                        {
                           score_error++;
                           total_error_count++;
                           return false;
                        }
                    }
                  })
                  if(score_error>0)
                  {
                     $("#row_error_"+id).text("{{__('admin.row_score_error')}}");
                     $("#row_error_"+id).show();
                     total_error_count++;
                     return false;
                  }

                  var question   = $("#question_"+id).val();
                  if(question=="")
                  {
                     $("#row_error_"+id).text("{{__('admin.row_question_error')}}");
                     $("#row_error_"+id).show();
                     total_error_count++;
                     return false;
                  }

               });
            }
            else
            {
               if(is_had_score=='1')
               {
                  if($(this).val()=="")
                  {
                     is_score_empty++;
                     total_error_count++;
                  }
              }
              var id_attr = $(this).attr("id");
              var id = "";
              if (id_attr) {
                  id = id_attr.split("_")[1];
               }
               if(id!="")
               {
                  var option_text         = $("#option_text_"+id).val();
                  var option_file_url     = $("#option_file_url_"+id).val();
                  var option_file         = $("#option_file_"+id)[0].files.length;
      
                  if(option_text =="" && option_file_url == "")
                  {
                     is_option_empty++;
                     total_error_count++;
                  }
                  var is_correct_answer = $("#is_correct_" + id).val();
                  $("#sord_order_" + id).val(sort_order);
                  sort_order++;
               }
               else
               {
                  is_option_empty=1;
               }
               if(is_score_empty>0 || is_option_empty >0)
               {
                  if(is_score_empty>0)
                  {
                     $("#row_error_"+id).text("{{__('admin.row_score_error')}}");
                     $("#row_error_"+id).show();
                     total_error_count++;
                     return false;
                  }
                  if(is_option_empty>0)
                  {
                     $("#row_error_"+id).text("{{__('admin.row_option_error')}}");
                     $("#row_error_"+id).show();
                     total_error_count++;
                     return false;
                  }
               }
            }
         });
         if(total_error_count==0)
         {
            $("#submit_type").val(type);
            $("#activity_form").submit();
         }
      }
   
   // Upload option images
   
   function upload_option_image_temp(file, row) {
       var imageData = new FormData();
       imageData.append('option_image', file);
       imageData.append('_token', '{{csrf_token()}}');
   
       $.ajax({
           url: "{{route('upload-option-image')}}",
           type: "POST",
           data: imageData,
           contentType: false,
           cache: false,
           processData: false,
           beforeSend: function() {
               $(".row_error").hide();
           },
           success: function(data) {
               var is_correct = $("#is_correct_" + row).val();
               var image_url = $("#option_file_" + row)[0].files[0];
               var option_text = $("#option_text_" + row).val();
               var score = $("#score_" + row).val();
   
               var temp = {};
   
               temp['row'] = row;
               temp['is_correct'] = is_correct;
               temp['image_url'] = data.url;
               temp['option_text'] = option_text;
               temp['option_id'] = "";
               temp['score'] = score;
               options[row] = temp;
   
               var asset = '{{asset("")}}';
               var file = asset + '/' + data.url;
   
               $('#preview_option_image_' + row).attr('src', file);
               $("#preview_img_" + row).show();
               $("#preview_img_" + row).attr("style", "display:block");
               $("#option_text_" + row).val("");
               $("#option_text_" + row).prop("disabled", true);
               $("#option_file_url_" + row).val(data.url);
   
           },
           error: function(e) {
               var json = JSON.parse(e.responseText);
               $("#row_error_" + row).text(json.message);
               $("#row_error_" + row).show();
           }
       });
   
   }
   
   function chagne_may_respond_value() {
       var value = $("input[name='may_respond']:checked").val();
       if (value == '2') {
           $("#may_respond_count").val("");
       }
   }
   
   function chagne_may_select_value() {
       var value = $("input[name='may_select']:checked").val();
       if (value == '2') {
           $("#may_select_count").val("");
       }
   }
   function copy_url(url)
   {
      navigator.clipboard.writeText(url)
        .then(() => {
          $("#copy_url").text("Copied!");
          setTimeout(function(){
            $("#copy_url").text("{{__('admin.copy_response_link')}}");
          },3000);
        })
        .catch((error) => {
          
        });
   }
   function toggle_text_message(type="")
   {
      if($("#response_type_2").is(":checked")==true)
      { 
         $(".text_content").show();
         set_options_name(type);
      } 
      else 
      { 
         $(".text_content").hide();
         $(".option_name").hide();
      }
   }
   
   function set_options_name(is_value="")
   {
      if($("#response_type_2").is(":checked")==true)
      {
         if(is_value=="")
         {
            var type = $("#option_text_type").val();
            if(type=="1")
            {
               var no = 1;
               $(".option_name").each(function(){
                  $(this).text(numberToAlphabetic(no));
                  var id = $(this).attr("id").split('_')[2];
                  $("#option_name_value_"+id).val(numberToAlphabetic(no));
                  no++;
               });
            }
            else if(type=="2")
            {
               var no = 1;
               $(".option_name").each(function(){
                  $(this).text(no);
                  var id = $(this).attr("id").split('_')[2];
                  $("#option_name_value_"+id).val(no);
                  no++;
               });
            }
         }
         $(".option_name").show();
      }
   }
   
   function numberToAlphabetic(number) 
   {
      let result = '';
      const base = 26;
   
      while (number > 0) {
         const remainder = (number - 1) % base;
         result = String.fromCharCode(65 + remainder) + result;
         number = Math.floor((number - 1) / base);
      }
   
      return result;
   }

   
</script>
@endpush