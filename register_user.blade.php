@extends('laravel-authentication-acl::admin.layouts.base-2cols')
@section('title')
Admin area: Registered User
@stop
@section('content')
{!! Charts::assets() !!}

<div class="col-md-12">
  <form method="post" action="{{URL::to('admin/customNewseller')}}" >
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <div class="row">
      <div class="form-group">
        <div class="col-md-3">
          <input class="form-control" id="start" name="start" data-toggle="datepickerSt" placeholder="{{__('header.Start Date')}}" value="" type="datepicker" required="true">
        </div>
        <div class="col-md-3">
          <input class="form-control" id="end" name="end" data-toggle="datepickerEn" placeholder="{{__('header.End Date')}}" type="" required>
        </div>
        <div class="col-md-3">
          <input class="btn btn-info" type="submit" name="submit" value="{{__('header.Submit')}}">
        </div>
      </div>
    </div>
  </form>
</div>

<br>
<br>
<div class="col-md-12 clearfix">
  <!--20181114 Changed by SHC PL:20180820_CP_001 - count put at same row-->
  <h2>Register Seller this Month: {{$daysum}} </h2>
  <!--<p>{{$daysum}}</p>-->
  @if($chart!=null)
    {!! $chart->render() !!}
  @endif

  @if($customcount==null)
  @else
   <h2 style="margin-top: 20px;">Selected Date New Seller: {{$customcount}}</h2>
  @endif
</div>

@stop

@section('footer_scripts')
<script type="text/javascript">
   $(document).ready(function(){

      $('[data-toggle="datepickerSt"]').datepicker({
        format: 'yyyy-mm-dd'
      });

      $('[data-toggle="datepickerEn"]').datepicker({
        format: 'yyyy-mm-dd  23:59:59'
      });
      
      $('[data-toggle="datepicker"]').datepicker();

  });
</script>
@stop