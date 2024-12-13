@extends('admin.email.base')
@section('content')
<table class="inner-body table-responsive" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
   <tr>
      <td class="content-cell">
         <h4><b>{{__('admin.hello')}} {{$mailData['data']['email']}}</b></h4>
         @isset($mailData['data']['message'])
            @foreach($mailData['data']['message'] as $value)
               @if($value=="")
                  <br>
               @else
                  <p>{{$value}}</p>
               @endif
            @endforeach
         @endisset
      </td>
   </tr>
</table>
@endsection