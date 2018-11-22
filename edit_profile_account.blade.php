@php

if(!$profileData)
    $profileData = [];
$selectFieldDisabled = '';
if($profileData['phone_number'] != '')
    $selectFieldDisabled = 'disabled="disabled"';

$padding = '';

if($profileData['account_type'] == 1)
{
    $flag = 0;
    $username = __('header.Username');
    $padding = 'padding-top-150';
    $profileImageText = __('header.Click Upload Profile Photo');
    $labelClass = 'padding-right: 208px;';
}
else
{
    $flag = 1;
    $username = __('header.Online Store Name');
    $labelClass = 'padding-right: 144px;';
    $profileImageText = __('header.Click Upload Company Logo');
}

$imageRef = asset('images/Asset 1-8.png');

if(!is_null($userImage))
    $imageRef = asset('profile_image/' . $userImage);

@endphp
@extends('layouts.app')
@section('content')
@section('pageContent')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form enctype="multipart/form-data" class="form-horizontal"  method="POST" action="/updateProfileAccount">
                        {{ csrf_field() }}

                        @if(Session::has('profileError'))
                        <div class="alert alert-danger">
                            @foreach(Session::get('profileError') as $error)
                            {{$error}}<br /n>
                            @endforeach
                        </div>
                        @endif

                        <div class="col-md-12 text-center">
                            <h4 style="color:black; font-size:24px;font-weight: bold;">{{__('header.My Account')}}</h4>
                        </div>

                        <div class="row">
                            @if(Auth::user()->provider!='facebook')
                                <div class="col-md-12" style="padding-top: 20px;">
                                    <div class="" style="display: table; margin: 0 auto; width: 160px; position: relative;">
                                        <div class="row text-center">
                                            @if ($errors->has('imgUpload'))
                                                <span class="clearfix text-danger">
                                                    <strong>{{ $errors->first('imgUpload') }}</strong>
                                                </span>
                                            @endif
                                            <span class="span-img" style="font-size: 12px;color: #ffffff; position: absolute;left: 75px;top: 66px;cursor: pointer;">{!!$profileImageText!!}</span>
                                            <img src="{{$imageRef}}" class="rounded-img-160 img-ico" style="cursor: pointer;" />
                                            <div>
                                                @if($profileData['account_type'] == 0)
                                                    <a class="btn btn-sm" style="background-color: black;color: white;font-size: 10px;cursor: default;">NGO</a>
                                                @elseif($profileData['account_type'] == 2)
                                                    <a class="btn btn-sm" style="background-color: black;color: white;font-size: 10px;cursor: default;">Corporate Seller</a>
                                                @endif
                                            </div>
                                            <input id="imgUpload" name="imgUpload" type="file" accept="image/png, image/jpeg, image/bmp, image/gif, image/jpg" class="file-upload" style="visibility: hidden;" />
                                        </div>
                                    </div>
                                </div>  
                            @endif
                        </div>

                        <div class="col-md-4 col-md-offset-4">
                            
                            @if($profileData['account_type'] == 0)
                            <div class="form-group">
                                <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.NGO Category')}}</label>
                                <div id="ngoCat" class="{{ $errors->has('ngoCategory') ? ' has-error' : '' }}">
                                    {{Form::select('ngoCategory', $ngoCategories, $profileData['ngo_category'], ['class'=>'form-control','placeholder'=> __('header.Select NGO category')], [])}}
                                    @if ($errors->has('ngoCategory'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('ngoCategory') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="form-group">
                                <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{$username}}</label>
                                <div class="{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <input id="first_name" type="text" class="form-control bg-white color-grey text-center" name="first_name" style="border:1px solid #939598;" value="{{ $profileData['first_name'] }}" placeholder="{{$username}}" required disabled>

                                    @if ($errors->has('first_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.Email Address')}}</label>
                                <div class="{{ $errors->has('email') ? ' has-error' : '' }} ">
                                    <input placeholder="{{__('header.Email Address')}}" id="email" type="email" class="form-control bg-white color-grey text-center" name="email" style="border:1px solid #939598;" value="{{ $profileData['email'] }}" required disabled>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.Mobile Phone No')}}.</label>
                                <div class="{{ $errors->has('phone_number') ? ' has-error' : '' }} ">
                                    
                                    <!--20181030 changed by SHC 20180820_CP_003 --> 
                                    <!--<input placeholder="{{__('header.Mobile phone')}}" id="phone_number" type="text" class="form-control bg-white color-grey text-center" style="border:1px solid #939598;" name="phone_number" value="+6{{ phone_number_format($profileData['phone_number']) }}" required disabled>-->

                                    <input placeholder="{{__('header.Mobile phone')}}" id="phone_number" type="text" class="form-control bg-white color-grey text-center" style="border:1px solid #939598;" name="phone_number" value="{{ $profileData['phone_number'] }}" required >
                                    
                                    @if ($errors->has('phone_number'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone_number') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-center" style="padding-top: 40px;">
                          <a href="{{route('showprofile')}}" class="btn btn-maroon" style="font-size: 15px;width: 90px;font-weight: normal; position: relative;top: 0; left: 0; padding-right: 10px; margin-right: 20px;">{{__('header.Cancel')}}</a>
                          <button type="submit" class="btn btn-primary" id="confirmBtn" style="font-size: 15px;font-weight: normal;width: 90px;">{{__('header.Confirm')}}</button>
                        </div>

                        {{-- <div class="row submitButtons">
                            <div class="col-md-6">
                                <button id="signupbutton" type="submit" class="btn btn-md btn-primary margin-left-13">
                                <i class="fa fa-btn fa-user"></i>{{__('header.Confirm')}}</button>
                                <a href="{{ route('showprofile') }}" class="btn btn-md btn-primary">
                                    {{__('header.Cancel')}}
                                </a>
                            </div>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
   <script type="text/javascript">

    $(document).ready(function (){
        //$('#phone_number').inputmask({ regex: "\\d{0,11}" });

        //20181030 changed by SHC 20180820_CP_003
        $('#phone_number').inputmask({"mask": "999-99999999"});
        $('.span-img').click(function(){
            $('#imgUpload').trigger('click');
        });

        $('.img-ico').click(function(){
            $('#imgUpload').trigger('click');
        });

        $(document).on('change', '#imgUpload', function()
        {
            var fileObject = $(this);
            var imageObject = $('.img-ico');

            if(fileObject!=undefined)
            {
                var fileObj = fileObject;
                if (fileObj[0].files[0] && (fileObj[0].files[0].type == "image/png" || fileObj[0].files[0].type == "image/jpeg" || fileObj[0].files[0].type == "image/gif"))
                {
                    var reader = new FileReader();
                    reader.onload = function (e)
                    {
                        var target = e.target || e.srcElement;
                        $(imageObject)
                            .attr('src', target.result)
                            .css('width', '100%');
                    }
                    reader.readAsDataURL(fileObj[0].files[0]);
                    $(imageObject).attr('title', fileObj[0].files[0].name);
                }
                else
                {
                    $(imageObject).attr('title', "");
                }
            }
        });
    });
</script>
@endsection
@section('additional_js')
<script>
  $('select').selectric();
</script>
@endsection
@include('layouts.ctp_side_menu')
@php
   $smallFooter = true;
@endphp
@endsection