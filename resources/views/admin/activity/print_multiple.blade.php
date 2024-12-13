<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>@isset($activity_data->title){{$activity_data->title}}@endisset</title>
      <style>
        body {
          margin: 0;
          padding: 0;
          font-family: Arial, sans-serif;
        }

        .content {
          width: 21cm;
          /* A4 width */
          height: 29.7cm;
          /* A4 height */
          margin: 0 auto;
          padding: 2cm;
          /* Padding for content */
          box-sizing: border-box;
          border: 1px solid #ccc;
        }

        @media print {

          body,
          .content {
            width: 100%;
            /* Full width for printing */
            height: 100%;
            /* Full height for printing */
            margin: 0;
            padding: 0;
            border: none;
          }
        }

        /* Reset some default styles */
        body,
        h1,
        h2,
        h3,
        p,
        table {
          margin: 0;
          padding: 0;
          font-size: 15px;
        }

        /* Set up the A4 size and center content */
        @page {
          size: A4;
          margin: 2;
        }

        /* Title */
        .title {
          text-align: center;
          font-size: 24px;
          margin-bottom: 20px;
        }

        /* Table styles */
        table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 20px;
          font-size: 11px;
        }

        th,
        td {
          padding: 10px;
        }

        th {
          background-color: #f3f3f3;
        }

        table,
        th,
        td {
          border: 1px solid #ccc;
        }
      </style>
   </head>
   <body>
      <div class="content">
         <h1 class="title">@isset($activity_data->title){{$activity_data->title}}@endisset</h1>
         <br>
         <h2>{{__('admin.summary')}}</h2>
         <table class="table table-hover" width="100%" align="left" style="margin-top: 0.5rem;">
            <thead>
               <tr>
                  <th colspan="2" style="text-align: left;">{{__('admin.response')}}</th>
                  @isset($activity_data->is_had_score)
                  @if($activity_data->is_had_score==1)
                  <th style="text-align: center;">{{__('admin.score')}}</th>
                  @endif 
                  @endisset
                  <th style="text-align: center;">{{__('admin.count')}}</th>
                  @isset($activity_data->is_had_score)
                  @if($activity_data->is_had_score==1)
                  <th style="text-align: center;">{{__('admin.total_score')}}</th>
                  @endif 
                  @endisset
               </tr>
            </thead>
            <tbody> 
               @php $total_count = 0;$total_score = 0;$total_final_score = 0; @endphp
                @foreach($option_data as $value)
                    @php $is_used = 0; @endphp
                    @foreach($value['options'] as $value1)
                        <tr>
                            @if($is_used==0)
                            <td rowspan="{{count($value['options'])}}" style="border-right: 1px solid lightgray;text-align: left;">{{$value['question']}}</td>
                            @php $is_used++; @endphp
                            @endif
                           <td style="text-align: left;">{{$value1['option']}}</td>
                            @isset($activity_data->is_had_score)
                            @if($activity_data->is_had_score==1)
                           <td style="text-align: center;">{{$value1['score']}}</td>
                           @endif
                           @endisset
                           <td style="text-align: center;">{{$value1['select_count']}}</td>
                           @isset($activity_data->is_had_score)
                            @if($activity_data->is_had_score==1)
                           <td style="text-align: center;">{{$value1['select_count']*$value1['score']}}</td>
                           @endif
                           @endisset
                        </tr>
                    @php
                        $total_count += $value1['select_count'];
                        $total_score += ($value1['score'])*($value1['select_count']);
                        $total_final_score += ($value1['score'])*($value1['select_count']);
                    @endphp
                    @endforeach
                @endforeach
                @if(count($option_data)==0)
                <tr>
                    @if($activity_data->is_had_score==1)
                    <td colspan="5" style="text-align: center;"><b>{{__('admin.no_response_available')}}</b></td>
                    @else
                    <td colspan="4" style="text-align: center;"><b>{{__('admin.no_response_available')}}</b></td>
                    @endif
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    @if($activity_data->is_had_score==1)
                    <td colspan="3" align="right"><b>{{__('admin.total')}}</b></td>
                    @else
                    <td align="right"><b>{{__('admin.total')}}</b></td>
                    @endif
                    @if($activity_data->is_had_score==1)
                    <td align="center">{{$total_count}}</td>
                    <td align="center">{{$total_final_score}}</td>
                    @else
                    <td></td>
                    <td align="center">{{$total_count}}</td>
                    @endif
                </tr>
            </tfoot>
         </table>
         <br>
         <table class="table table-hover" width="100%" style="margin-top: 0.5rem;">
            <thead>
               <tr>
                  <th style="text-align: left;">{{__('admin.how_people_responded')}}</th>
                  <th style="text-align: center;">{{__('admin.count')}}</th>
               </tr>
            </thead>
            <tbody>
               @php $total_count = 0; @endphp
               @foreach($responseTypeData as $value)
               <tr>
                  <td style="text-align: left;">{{$value->response_from}}</td>
                  <td style="text-align: center;">{{$value->total_select}}</td>
               </tr>
               @php $total_count += $value->total_select; @endphp
               @endforeach
               @if(count($responseTypeData)==0)
               <tr>
                  <td style="text-align: center;" colspan="2" align="center"><b>{{__('admin.no_response_available')}}</b></td>
               </tr>
               @endif
            </tbody>
            <tfoot>
               <tr>
                  <td style="text-align: right;"><b>{{__('admin.total')}}</b></td>
                  <td style="text-align: center;"><b>{{$total_count}}</b></td>
               </tr>
            </tfoot>
         </table>
         <br>
         <h2>{{__('admin.indivitual_responses')}}</h2>
         <table class="table table-hover" style="margin-top: 0.5rem;">
            <thead>
               <tr>
                  <th style="text-align: left;">{{__('admin.response')}}</th>
                  <th style="text-align: left;">{{__('admin.via')}}</th>
                  <th style="text-align: left;">{{__('admin.screen_name')}}</th>
                  <th style="text-align: left;">{{__('admin.received_at')}}</th>
               </tr>
            </thead>
            <tbody>
               @foreach($responseAllData as $value)
               <tr>
                  <td style="text-align: left;"><label>{{$value->question}}</label><br><label>{{$value->option}}</label></td>
                  <td style="text-align: left;">{{$value->response_from}}</td>
                  <td style="text-align: left;">{{$value->unique_name}}</td>
                  <td style="text-align: left;">{{app_date_format($value->updated_at)}}</td>
               </tr>
               @endforeach
               @if(count($responseAllData)==0)
               <tr>
                  <td style="text-align: center;" colspan="4" align="center"><b>{{__('admin.no_response_available')}}</b></td>
               </tr>
               @endif
            </tbody>
         </table>
      </div>
      <script type="text/javascript">
         window.print();
      </script>
   </body>
</html>