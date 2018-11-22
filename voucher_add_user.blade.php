@extends('laravel-authentication-acl::admin.layouts.base-2cols')
@section('title')
Admin area: Add Voucher Details
@stop
@section('content')

<div class="col-md-12 col-sm-12 col-xs-12">
   <h3>Add Voucher Details</h3>
   <hr>
</div>

<div class="col-md-6">
  @if($status = Session::get('status'))
    <div class="alert alert-success">
      {{$status}}
    </div>
  @endif

  @if(Session::has('EmailError'))
     <div class="alert alert-danger">
       @foreach(Session::get('EmailError') as $error)
       {{$error}}<br /n>
       @endforeach
     </div>
  @endif

  <?php
    //Add by ChenCW 20181107
    $voucherMail = env('MAIL_VOUCHER', 'voucher_t@cilipadi.com');
  ?>
  

  <form method="post" action="{{URL::to('admin/setAddVoucher')}}" >
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <input type="hidden" name="tkn" value="{{$voucherCode}}">
    <input type="hidden" name="pkg" value="{{$packageId}}">
    <input type="hidden" name="typ" value="{{$flag}}">
    <div class="form-group">
      <label class="control-label">Voucher Code:</label>
      <input class="form-control" type="text" id="voucher" name="voucher" placeholder="" value="{{$voucherCode}}" required autofocus disabled="disabled">
    </div>

    <div class="form-group">
    <!-- 2018-12-31 change by ChenCW-->
      <input class="form-control" type="email" id="userEmail" name="userEmail" placeholder="Enter User Email" value="{{$voucherMail}}" readonly required autofocus>
    </div>
    
    <!-- 2018-12-31 add by ChenCW-->
    <div class="form-group">
      <label class="control-label">Voucher Expire Date:</label>
      <input class="form-control" type="Date" id="voucherExpire" name="voucherExpire" placeholder="" value=Carbon::now() required>
    </div>

    <input class="btn btn-info" type="submit" value="{{__('header.Submit')}}">
  </form>
</div>
@stop
@section('footer_scripts')
<script type="text/javascript">
   $(document).ready(function(){
      $('#userID').inputmask({ regex: "\\d{1,10}" });
   });
   </script>
@stop