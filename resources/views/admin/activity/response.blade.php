@extends('layouts.header')
@section('content')
<!-- Content -->
<style>
tbody tr {
    background-color: #F4F4F4;
}

th {
    font-weight: 400;
}
.over-flow_y {
    min-height: 10px;
    max-height: 1000px;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="">
        <div class="">
            <div class="row ">
                <div class="col-md-9 col-12  ">
                    <div class="box-shadow">
                        <h4 class="qust_lable">@isset($activity_data[0]->title){{$activity_data[0]->title}}@endisset
                        </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="qust_lable">{{__('admin.summary')}}</h4>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{__('admin.response')}}</th>
                                            @isset($activity_data[0]->is_had_score)
                                            @if($activity_data[0]->is_had_score==1)
                                            <th>{{__('admin.score')}}</th>
                                            @endif
                                            @endisset
                                            <th>{{__('admin.count')}}</th>
                                            @isset($activity_data[0]->is_had_score)
                                            @if($activity_data[0]->is_had_score==1)
                                            <th>{{__('admin.total_score')}}</th>
                                            @endif
                                            @endisset
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total_count = 0;$total_score = 0;$total_final_score = 0; @endphp
                                        @foreach($option_data as $value)
                                        <tr>
                                            <td>{{$value->option}}</td>
                                            @isset($activity_data[0]->is_had_score)
                                            @if($activity_data[0]->is_had_score==1)
                                            <td>{{$value->score}}</td>
                                            @endif
                                            @endisset
                                            <td>@if($value->select_count>0){{$value->select_count}}@else 0 @endif</td>
                                            @isset($activity_data[0]->is_had_score)
                                            @if($activity_data[0]->is_had_score==1)
                                            <td>@if($value->select_count>0){{($value->score)*($value->select_count)}}@else 0 @endif</td>
                                            @endif
                                            @endisset
                                        </tr>
                                        @php
                                        if($value->select_count>0)
                                        {
                                            $total_count += $value->select_count;
                                            $total_score += ($value->score)*($value->select_count);
                                            $total_final_score += ($value->score)*($value->select_count);
                                        }
                                        
                                        @endphp
                                        @endforeach
                                        @if(count($option_data)==0)
                                        <tr>
                                            @if($activity_data[0]->is_had_score==1)
                                            <td colspan="4"><b>{{__('admin.no_response_available')}}</b></td>
                                            @else
                                            <td colspan="2"><b>{{__('admin.no_response_available')}}</b></td>
                                            @endif
                                        </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            @if($activity_data[0]->is_had_score==1)
                                            <td colspan="2" align="right"><b>{{__('admin.total')}}</b></td>
                                            @else
                                            <td align="right"><b>{{__('admin.total')}}</b></td>
                                            @endif
                                            @if($activity_data[0]->is_had_score==1)
                                            <td>{{$total_count}}</td>
                                            <td>{{$total_final_score}}</td>
                                            @else
                                            <td>{{$total_count}}</td>
                                            @endif

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5 table-responsive">
                                <table class="table table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{__('admin.how_people_responded')}}</th>
                                            <th>{{__('admin.count')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total_count = 0; @endphp
                                        @foreach($responseTypeData as $value)
                                        <tr>
                                            <td>{{$value->response_from}}</td>
                                            <td>{{$value->total_select}}</td>
                                        </tr>
                                        @php $total_count += $value->total_select; @endphp
                                        @endforeach
                                        @if(count($responseTypeData)==0)
                                        <tr>
                                            <td colspan="2" align="center"><b>{{__('admin.no_response_available')}}</b>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><b>{{__('admin.total')}}</b></td>
                                            <td><b>{{$total_count}}</b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4>{{__('admin.indivitual_responses')}}</h4>
                            </div>
                            <div class="col-md-12">
                              <div class="table-responsive over-flow_y ">
                              <table class="table table-hover" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{__('admin.response')}}</th>
                                            <th>{{__('admin.via')}}</th>
                                            <th>{{__('admin.screen_name')}}</th>
                                            <th>{{__('admin.received_at')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($responseAllData as $value)
                                        <tr>
                                            <td>{{$value->option}}</td>
                                            <td>{{$value->response_from}}</td>
                                            <td>{{$value->unique_name}}</td>
                                            <td>{{app_date_format($value->updated_at)}}</td>
                                        </tr>
                                        @endforeach
                                        @if(count($responseAllData)==0)
                                        <tr>
                                            <td colspan="4" align="center"><b>{{__('admin.no_response_available')}}</b>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                              </div>
                                
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-3 col-12  ">
                    <div class="box-shadow">
                        <div class="row">
                            <div class="col-md-12" style="display:none">
                                <p onclick="toggle_response()" style="cursor:pointer;"><i class="fa fa-share-alt" aria-hidden="true"></i>
                                    {{__('admin.share_responses')}}</p>
                            </div>
                            <div class="col-md-12" id="toggle_response"
                                style="display:@if(session('success') || $errors->any()){{'block'}}@else{{'none'}}@endif;">
                                <div class="row">
                                    <div class="col-md-12">
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
                                    </div>
                                    <form method="POST" action="{{route('share-responses')}}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="@isset($activity_data[0]->id){{app_encode($activity_data[0]->id)}}@endisset">
                                            <div class="col-md-12">
                                                <input type="radio" onchange="toggle_response_via()" name="response_via" id="response_via_1" value="email" checked> {{__('admin.email')}} &emsp;
                                                <input type="radio" onchange="toggle_response_via()" name="response_via" id="response_via_1" value="sms"> {{__('admin.sms')}}
                                            </div>
                                        <div class="col-md-12" id="email_div" style="display:none;">
                                            <textarea class="form-control" name="email" id="email" rows="5" placeholder="{{__('admin.use_comma')}}" required></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <br>
                                            <button type="submit"
                                                class="btn btn-primary btn-icons">{{__('admin.share_responses')}}</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-12"><br></div>
                            </div>
                            <div class="col-md-12">
                                <p style="cursor:pointer;"
                                    onclick="window.location.href='{{url("/")}}/download-excel/{{app_encode($activity_data[0]->id)}}'">
                                    <i class='bx bx-download'></i> {{__('admin.download_spreadsheet')}}
                                </p>
                            </div>
                            <div class="col-md-12">
                                <p style="cursor:pointer;"
                                    onclick="window.open('{{url("/")}}/print-response/{{app_encode($activity_data[0]->id)}}', '_blank');">
                                    <i class='bx bx-printer'></i> {{__('admin.print')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
$(document).ready(function(){
    toggle_response_via();
});
function toggle_response() {
    $("#toggle_response").toggle();
}

function toggle_response_via()
{
    var type = $('input[name="response_via"]:checked').val();
    if(type=="email")
    {
        $("#email_div").show();
        $("#email").attr("required");
    }
    else
    {
        $("#email_div").hide();
        $("#email").removeAttr("required");
        $("#email").val("");
    }
}
</script>
@endpush