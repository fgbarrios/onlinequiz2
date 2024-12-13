@php
$message = trans('visitors.multiple_question_message');
@endphp
<style type="text/css">
   /* Your CSS Styles */
   .bar {
   background: #cfcfcf;
   border-bottom: 10px solid white;
   }
   .active {
   background: #211c4e !important;
   border-bottom: 10px solid white;
   color: white;
   }
   .box {
   background: white;
   text-align: center;
   padding: 8px 20px;
   color: #5c5c5c;
   border-radius: 5px;
   font-weight: 700;
   }
   .pointer {
   cursor: pointer;
   }
   .title-image {
   display: block;
   width: auto;
   height: auto;
   max-width: 100%;
   max-height: 50vh;
   margin: 0 auto;
   }
   .option-image {
   display: block;
   width: auto;
   height: auto;
   max-width: 100%;
   max-height: 50vh;
   margin: 0 auto;
   }
   .del_btn {
   display: none;
   }
   .trash:hover {
   color: #853802;
   }
   .slider-container {
   overflow: hidden;
   width: 100%;
   margin: 0 auto;
   }
   .slider-wrapper {
   display: flex;
   transition: transform 1.8s ease;
   }
   .slider-item {
   flex: 0 0 100%; /* Occupies full width of container */
   padding: 20px;
   }
   .bar1 {
   padding: 10px;
   }
   .bar2 {
   background: #cfcfcf;
   margin-bottom: 10px;
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
         @isset($option_data)
         <div class="container" style="padding: 1rem; background: #f2f2f273;">
            <div class="slider-container">
               <div class="slider-wrapper">
                  @foreach($option_data as $value)
                  <div class="slider-item">
                     <div class="row">
                        <div class="col-md-6 col-12 bar1">
                           <label>{{$value['question']}}</label>
                        </div>
                        <div class="col-md-6 col-12">
                           @isset($value['options'])
                           @foreach($value['options'] as $value1)
                           <div class="row bar1 bar2 @if($value1['select_count']>0) {{'active'}} @endif bar_{{$value1['id']}}" id="opt_{{$value1["id"]}}">
                           <div class="col-md-11 col-10" style="cursor:pointer;" onclick="add_voting('{{app_encode($value["question_id"])}}','{{$value1["id"]}}','{{$value["question_id"]}}')">
                           <label>{{$value1['option']}}</label>
                        </div>
                        <div class="col-md-1 col-1" id="delete_div_{{$value1['id']}}" align="center" @if($value1['select_count']>0) style="display:block" @else style="display:none" @endif onclick="remove_voting('{{app_encode($value["question_id"])}}','{{$value1["id"]}}','{{$value["question_id"]}}')">
                        <label id="delete_vote_{{$value1['id']}}" class="del_btn" style="display:block;">
                        <i class="bx bx-trash trash" style="cursor:pointer; font-size: 2rem;"></i>
                        </label>
                     </div>
                  </div>
                  @endforeach
                  @endisset
               </div>
               <input type="hidden" name="total_count_{{$value['question_id']}}" id="total_count_{{$value['question_id']}}" value="{{$value['total_count']}}">
               <input type="hidden" name="available_count_{{$value['question_id']}}" id="available_count_{{$value['question_id']}}" value="{{$value['available_count']}}">
               <input type="hidden" name="select_count_{{$value['question_id']}}" id="select_count_{{$value['question_id']}}" value="{{$value['select_count']}}">
            </div>
         </div>
         @endforeach
      </div>
   </div>
   <div class="text-center mt-3">
      <button class="btn btn-primary" id="prevBtn" style="display:none;">{{__('visitors.previous')}}</button>
      <button class="btn btn-primary" id="nextBtn">{{__('visitors.next')}}</button>
      <button class="btn btn-primary" type="button" id="submit_btn" style="display:none;">{{__('visitors.submit')}}</button>
   </div>
</div>
@endisset
</div>
<div class="col-md-12" align="center" id="response_result_div" style="display:none;">
      <br>
      <div class="alert alert-danger" id="response_result_class" role="alert" align="center">
         <label id="response_result"></label>
      </div>
   </div>
</div>
</div>
<input type="hidden" name="activity_id" id="activity_id" value="{{app_encode($activity_data->id)}}">
<script type="text/javascript">
   is_submit_multiple=0;
   $(document).ready(function () {
       let sliderWrapper = document.querySelector('.slider-wrapper');
       let sliderItems = document.querySelectorAll('.slider-item');
       let prevBtn = document.getElementById('prevBtn');
       let nextBtn = document.getElementById('nextBtn');
   
       let currentIndex = 0;
   
       // Function to update the slider position
       function updateSlider() {
           const itemWidth = sliderItems[0].offsetWidth;
           sliderWrapper.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
       }
   
       // Event listener for "Next" button
       nextBtn.addEventListener('click', function () {
           if (currentIndex < sliderItems.length - 1) {
               currentIndex++;
           } 
           // else {
           //     currentIndex = 0; // Loop back to the first div
           // }
           
           action_perform();
       });
   
       // Event listener for "Previous" button
       prevBtn.addEventListener('click', function () {
           if (currentIndex >= 0) {
               currentIndex--;
           } 
           // else {
           //     currentIndex = sliderItems.length - 1; // Loop to the last div
           // }
           action_perform();
       });
       let is_stop = 0;
       function action_perform() 
       {
           if(currentIndex==0)
           {
                $("#prevBtn").hide();
                $("#nextBtn").text("{{__('visitors.next')}}");
                $("#nextBtn").removeAttr("onclick");
                $("#submit_btn").hide();
                $("#nextBtn").show();
           }
           else if(currentIndex=={{count($option_data)-1}})
           {
               $("#submit_btn").show();
               $("#nextBtn").hide();
           }
           else
           {
                $("#nextBtn").text("{{__('visitors.next')}}");
                $("#nextBtn").removeAttr("onclick");
                $("#prevBtn").show();
                $("#submit_btn").hide();
                $("#nextBtn").show();
           }
           if(is_stop==0)
           {
             updateSlider();
           }
       }
   });
   $("#submit_btn").click(function(){
      submit_answers_multiple();
   });
</script>
