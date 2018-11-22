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
    $labelClass = 'padding-right: 220px;';
}
else
{
    $flag = 1;
    $username = __('header.Online Store Name');
    $labelClass = 'padding-right: 154px;';
}

$imageRef = asset('images/Asset 1-8.png');

if(!is_null($userImage))
    $imageRef = asset('profile_image/' . $userImage);


$locations = [];
$ngoCatId = '';

$postlocations = [];
foreach ($location as  $loc) {
    $postlocations[$loc['id']] = $loc['name']; 
}


#<!--20181025 Change by SHC 20180816_CP_002 Start -->
$imageRef1 = asset('images/images/post_image_placeholder.png');
$imageRef2 = asset('images/post_image_placeholder.png');
$imageRef3 = asset('images/post_image_placeholder.png');
$imageRefToClick1= '';
$imageRefToClick2= '';
$imageRefToClick3= '';
$imageRefToClick=$imageRef3;

if(!is_null($profileData['company_certificate_url'])){
    $imageRef1 = asset('getCert/' . $profileData['company_certificate_url']);
    if (strtoupper(substr($imageRef1,strlen($imageRef1)-3,3))=='PDF'){
        $imageRefToClick1= $imageRef1  ;
        $imageRef1 = asset('images/PDF.png');
    }
}

if(!is_null($profileData['company_certificate_url2'])){
    $imageRef2 = asset('getCert/' . $profileData['company_certificate_url2']);
    if (strtoupper(substr($imageRef2,strlen($imageRef2)-3,3))=='PDF'){
        $imageRefToClick2= $imageRef2 ;
        $imageRef2 = asset('images/PDF.png');
    }
}

if(!is_null($profileData['company_certificate_url3'])){
    $imageRef3 = asset('getCert/' . $profileData['company_certificate_url3']);
    if (strtoupper(substr($imageRef3,strlen($imageRef3)-3,3))=='PDF'){
        $imageRefToClick3= $imageRef3 ;
        $imageRef3 = asset('images/PDF.png');
    }
}
#<!--20181025 Change by SHC 20180816_CP_002 End -->

@endphp
@extends('layouts.app')
@section('content')
@section('pageContent')

<img src="{{asset('images/web-house.png')}}" class="img-responsive pull-right" alt="Home" onclick="window.location='{{route('home')}}'" style="height: 30px;cursor:pointer;" />

@if(Session::has('successAccount'))
    <div class="alert alert-success successMsg">
        {{Session::get('successAccount')}}
    </div>
@endif

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-horizontal">
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

                        @if(Auth::user()->provider!='facebook')
                            <div class="col-md-12" style="padding-top: 20px;">
                                <div class="row text-center">
                                    @if ($errors->has('imgUpload'))
                                        <span class="clearfix text-danger">
                                            <strong>{{ $errors->first('imgUpload') }}</strong>
                                        </span>
                                    @endif
                                    <img src="{{$imageRef}}" class="rounded-img-160 img-ico" />
                                    <div>
                                        @if($profileData['account_type'] == 0)
                                            <a class="btn btn-sm" style="background-color: black;color: white;font-size: 10px;cursor: default;">NGO</a>
                                        @elseif($profileData['account_type'] == 2)
                                            <a class="btn btn-sm" style="background-color: black;color: white;font-size: 10px;cursor: default;">Corporate Seller</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($profileData['account_type'] == 0)
                            <div class="form-group col-md-12" style="margin-bottom: 0px;">
                                <div class="row text-center" style="padding-right: 200px;">
                                    <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.NGO Category')}}</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-md-offset-4" style="padding-left: 49px;">
                                        <label class="form-control col-md-6 border-radius-2 color-grey-new text-center" style="font-weight:normal;border: 1px solid #939598;">{{ $ngoCategory[0]['category_name'] }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        
                        <div class="form-group col-md-12" style="margin-bottom: 0px;">
                            <div class="row text-center" style="{{$labelClass}}">
                                <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{$username}}</label>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4" style="padding-left: 49px;">
                                    <label class="form-control col-md-6 border-radius-2 color-grey-new text-center" style="font-weight:normal;border: 1px solid #939598;">{{ $profileData['first_name'] }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-12" style="margin-bottom: 0px;">
                            <div class="row text-center" style="padding-right: 200px;">
                                <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.Email Address')}}</label>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4" style="padding-left: 49px;">
                                    <label class="form-control col-md-6 border-radius-2 color-grey-new text-center" style="font-weight:normal;border: 1px solid #939598;">{{ $profileData['email'] }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-12" style="margin-bottom: 0px;">
                            <div class="row text-center" style="padding-right: 182px;">
                                <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.Mobile Phone No')}}.</label>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4" style="padding-left: 49px;">
                                    <label class="form-control col-md-6 border-radius-2 color-grey-new text-center" style="font-weight:normal;border: 1px solid #939598;">+6{{ phone_number_format($profileData['phone_number']) }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-12" style="margin-top: 20px;margin-left: 5px;">
                            <div class="row text-center">
                                <a href="{{route('editAccountInfo')}}" class="btn btn-lg btn-primary" style="font-size: 15px;border-radius: 2px;">{{__('header.Edit Profile')}}</a>
                            </div>
                            <div class="row text-center" style="padding-top: 7px;">
                                <a href="{{ route('changepassword')}}" style="font-size: 12px;border-radius: 2px; color: #939598;">{{__('header.Change Password')}}</a>
                            </div>
                        </div>



                        @if($flag == 1)
                            <div class="row col-md-12">
                                <fieldset class="profileInfo margin-10-px">
                                    <legend style="color:black; font-size:24px;font-weight: bold;border-bottom: 3px solid #000000;">{{__('header.Company Information')}}</legend>
                                </fieldset>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="row">
                                        <label style="color:black;font-size: 11px;margin-bottom: 0px; padding-left: 16px;">{{__('header.Company Name')}}</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">{{ $profileData['company_name'] }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 0px; margin-top: 5px;">
                                    <div class="row">
                                        <label style="color:black;font-size: 11px;margin-bottom: 0px; padding-left: 16px;">{{__('header.Company Registration No')}}.</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">{{ $profileData['company_register_num'] }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 0px; margin-top: 5px;">
                                    <div class="row">
                                        <label style="color:black;font-size: 11px;margin-bottom: 0px; padding-left: 16px;">{{__('header.Full Address')}}</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">{{ $profileData['address_1'] }}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">{{ $profileData['address_2'] }}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">{{ $profileData['address_postcode'] }} {{ $profileData['address_city'] }}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">{{ $postlocations[$profileData['address_state']] }}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">{{__('header.Malaysia')}}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 0px; margin-top: 5px;">
                                    <div class="row">
                                        <label style="color:black;font-size: 11px;margin-bottom: 0px; padding-left: 16px;">{{__('header.Phone No')}}.</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">
                                            <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">+6{{ phone_number_format($profileData['company_phone_num']) }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 0px; margin-top: 5px;">
                                    <div class="row">
                                        <label style="color:black;font-size: 11px;margin-bottom: 0px; padding-left: 16px;">{{__('header.Fax No')}}.</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="">                                            
                                            <!--20181105 Changed by SHC PL:20181011_CP_003-->
                                            <?php if($profileData['company_fax_num']!=''): ?>
                                                <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;">+6{{ phone_number_format($profileData['company_fax_num']) }}</label>
                                            <?php else: ?>
                                                <label class="form-control border-radius-2 color-grey-new" style="font-weight:normal;border: 1px solid #939598;font-size:15px;"></label>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                          
                           <!--20181105 Changed by SHC PL:20181011_CP_003 Start-->
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="pull-right">
                                        <div class="col-md-12 text-center">

                                            <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.Company ROS/ROC Certificate')}}</label>
                                            <div style="width: 185px;height: 230px; border: 3px solid #000000;">

                                            @if($imageRefToClick1 != '')                
                                                <a href="#" data-toggle="modal" data-target="#PicModal" onclick="FnImageClick(1)">
                                            @endif
                                                <img style="width: 100%;height: 100%;object-fit: contain;object-position: center center;" src="{{$imageRef1}}" style="" />
                                            @if($imageRefToClick1 != '')
                                                </a>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="row">
                                    <div class="pull-right">
                                        <div class="col-md-12 text-center">
                                            <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.Company ROS/ROC Certificate')}}</label>
                                            <div style="width: 185px;height: 230px; border: 3px solid #000000;">
                                                         
                                            @if($imageRefToClick2 != '')
                                                <a href="#" data-toggle="modal" data-target="#PicModal" onclick="FnImageClick(2)">
                                            @endif
                                                <img style="width: 100%;height: 100%;object-fit: contain;object-position: center center;" src="{{$imageRef2}}" style=""  />
                                            @if($imageRefToClick2 != '')
                                                </a>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="row">
                                    <div class="pull-right">
                                        <div class="col-md-12 text-center">
                                            <label style="color:black;font-size: 11px;margin-bottom: 0px;">{{__('header.Company ROS/ROC Certificate')}}</label>
                                            <div style="width: 185px;height: 230px; border: 3px solid #000000;">
                                            @if($imageRefToClick3 != '')
                                                <a href="#" data-toggle="modal" data-target="#PicModal"  onclick="FnImageClick(3)">
                                            @endif
                                                <img style="width: 100%;height: 100%;object-fit: contain;object-position: center center;" src="{{$imageRef3}}" style="" /> 
                                            @if($imageRefToClick3 != '')
                                                </a>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           <!--20181105 Changed by SHC PL:20181011_CP_003 End-->

                            <div class="form-group col-md-12" style="margin-top: 20px;margin-left: 5px;">
                                <div class="row text-center">
                                    <a href="{{route('editCompanyInfo')}}" class="btn btn-lg btn-primary" style="font-size: 15px;border-radius: 2px;width: 200px;">{!!__('header.Edit Company Information')!!}</a>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="PicModal" role="dialog">
      <div class="modal-dialog modal-lg model-auto">
         <!-- Modal content-->
         <div class="modal-content bg-login">
            <div class="modal-body">
                  <iframe id="framepdf1" name="framepdf1" src="{{$imageRefToClick1}}?#zoom=100" style="width:100%;height:700px;" hidden="true" ></iframe>
                  <iframe id="framepdf2" name="framepdf2" src="{{$imageRefToClick2}}?#zoom=100" style="width:100%;height:700px;" hidden="true" ></iframe>
                  <iframe id="framepdf3" name="framepdf3" src="{{$imageRefToClick3}}?#zoom=100" style="width:100%;height:700px;" hidden="true" ></iframe>
              </div>
         </div>
      </div>
    </div>
@endsection

<script type="text/javascript">
    $(document).ready(function(){
        $('.successMsg').delay(4000).fadeOut('slow');
        //$('#loginModal').modal();
        $('img').bind('contextmenu',function(e){
            return false;
        })
        window.framepdf1.oncontextmenu=function(){
           return false;
        }
        window.framepdf2.oncontextmenu=function(){
           return false;
        }
        window.framepdf3.oncontextmenu=function(){
           return false;
        }
    });
 
 function FnImageClick(simg){
    document.getElementById('framepdf1').hidden=true;
    document.getElementById('framepdf2').hidden=true;
    document.getElementById('framepdf3').hidden=true;
    if (simg==1){
    document.getElementById('framepdf1').hidden=false;
    }
    if (simg==2){
    document.getElementById('framepdf2').hidden=false;
    }
    if (simg==3){
    document.getElementById('framepdf3').hidden=false;
    }
    
   //document.getElementById('framepdf').contentwindow.location.reload();
}

 </script>

@include('layouts.ctp_side_menu')
@php
   $smallFooter = true;
@endphp
@endsection