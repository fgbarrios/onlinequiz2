@extends('admin.email.base')
@section('content')
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
   <tr>
      <td class="content-cell">
         <p><b>{{__('admin.hello')}} {{$mailData['data']['email']}}</b></p>
         <br>
         <p style="white-space:pre-line;">{{$mailData['data']['message']}}</p>
         <br>
         <p><a href="{{$mailData['data']['visit_url']}}" class="button button-green">{{__('admin.visit')}}</a></p>
      </td>
   </tr>
</table>
@endsection