@php
    $typeSelection = '';
    $ngoCatId = '';
    $stateid = '';
    $sessionType = Session::get('typeSelection');

    $locations = [];
    foreach ($stateData as  $loc) {
        $locations[$loc['id']] = $loc['name'];
    }

    if($sessionType!=null)
    {
        if($sessionType == 2)
        {
            $typeSelection = 'corporate';
        }
        if($sessionType == 0)
        {
            $typeSelection = 'ngo';
        }
        if($sessionType == 1)
        {
            $typeSelection = 'individual';
        }
    }

    $certImageText = __('header.Upload ROS/ROC Certificate');
    $imageRef = asset('images/post_image_placeholder.png');
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="m-123-0-0-0">
                    <h3 class="font-color">{{__('header.Welcome to')}}</h3>
                </div>
                <div style="margin: 0;">
                    <h4 class="font-color">{{__('header.Not Sure')}}</h4>
                </div>
                <div  style="margin: 0;">
                    <div class="tips">
                        <a href="#" id="tipsModalb" class="text-maroon" data-toggle="modal" data-target="#tipsModal">
                        <i class="glyphicon glyphicon-question-sign"></i>{{__('header.Tips')}}</a>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <form id="registerfrm" class="row form-horizontal" enctype="multipart/form-data" method="POST" action="{{ route('register') }}">
                    {{ csrf_field() }}

                    @if(Session::has('registerError'))
                        <div class="alert alert-danger">
                            @foreach(Session::get('registerError') as $error)
                                {{$error}}<br />
                            @endforeach
                        </div>
                    @endif

                    <div id="register_type" class="register-type clearfix">
                        <div class="col-md-4">
                            <div class="individual">
                                {{__('header.Individual Seller')}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="corporate">
                                {{__('header.Corporate Seller')}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ngo">
                                {{__('header.NGO')}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}" id="radioButtons" style="display: none;">

                        <div class="row">
                            <div class="col-md-9 col-md-offset-4">
                                <label class="radio-inline">
                                    <input type="radio" id="individual" name="type" class="radio" value="1"<?php if(old('type', 1)== "1") { echo 'checked="checked"'; } ?>>{{__('header.Individual')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="ngo" name="type" class="radio" value="0" <?php if(old('type')== "0") { echo 'checked="checked"'; } ?>>{{__('header.NGO')}}
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="corporate" name="type" class="radio" value="2" <?php if(old('type')== "2") { echo 'checked="checked"'; } ?>>{{__('header.Corporate')}}
                                </label>

                                @if ($errors->has('type'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('type') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="postErrorBox" style="display: none;">
                      <ul>
                      </ul>
                    </div>
                    <div class="clearfix"></div>
                    <div id="1st-ind" class="pad-14" style="display: none;">
                        {{-- <div class="fb-area" style="display: none;">
                            <hr style="border: 1px solid;margin-top: 67px; color: #424242;">
                            <h6 class="text-maroon">Continue With</h6>
                            <div class="row m-bottom-55">
                                <div class="col-md-3">
                                    <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook"><i class="fa fa-facebook"></i> Facebook</a>
                                </div>
                            </div>
                            <h6 class="text-maroon">or</h6>
                            <hr style="border: 1px solid;color: #424242;">
                        </div> --}}

                        <fieldset class="NgoCatInfo companyinfo" style="display: none;">
                            <legend id="hngo" style="display: none; color: #424242;">{{__('header.NGO Category')}}</legend>
                            <div class="row">
                                <div class="">
                                    <div class="">
                                        <div id="ngoCat" style="display: none;" class="col-md-5 padding-bottom-8 {{ $errors->has('ngoCategory') ? ' has-error' : '' }}">
                                            {{Form::select('ngoCategory', $ngoCategories, $ngoCatId, ['class'=>'form-control input-h-45 border-radius-2 text-grey','placeholder'=> __('header.Select NGO category')], [])}}
                                            @if ($errors->has('ngoCategory'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('ngoCategory') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="AccInfo companyinfo">
                            <legend>{{__('header.Account Information')}}</legend>
                        </fieldset>

                        <div id="nkname" class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }} col-md-5 ">
                            <label style="display: none;" for="first_name" id="username" class="col-md-4 control-label color-grey-new">{{__('header.Username')}}</label>
                         
                            <input placeholder="{{__('header.Username')}}" id="first_name" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="first_name" value="{{ old('first_name') }}" autofocus maxlength="20"> <!--20181025 Change by SHC 20180816_CP_001 -->

                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                        {{-- <div id="stname" style="display: none;" class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                            <label for="first_name" id="first_name" class="col-md-4 control-label">{{__('header.CTP online store name')}}*</label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" autofocus>

                                @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div> --}}


                        <div class="form-group col-md-5 {{ $errors->has('email') ? ' has-error' : '' }} ">
                            {{-- <label for="email" class="col-md-4 control-label">{{__('header.E-Mail Address')}}*</label> --}}

                            <!--20181025 Change by SHC 20180816_CP_001 -->
                            <!--<input placeholder="{{__('header.Email Address')}}" id="email" type="email" class="form-control input-h-45 border-radius-2 color-grey-new" name="email" value="{{ old('email') }}">-->
                            <input placeholder="{{__('header.Email Address')}}" id="email" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="email" value="{{ old('email') }}">

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-md-5 {{ $errors->has('phone_number') ? ' has-error' : '' }} ">
                            {{-- <label for="phone_number" class="col-md-4 control-label">{{__('header.Mobile Phone')}}*</label> --}}

                            <input placeholder="{{__('header.Mobile phone')}}" id="phone_number" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="phone_number" value="{{ old('phone_number') }}">

                            @if ($errors->has('phone_number'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone_number') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-md-5 {{ $errors->has('password') ? ' has-error' : '' }} ">
                            {{-- <label for="password" class="col-md-4 control-label">{{__('header.Password')}}*</label> --}}

                            <input placeholder="{{__('header.Password')}}" id="password" type="password" class="form-control input-h-45 border-radius-2 color-grey-new" name="password">

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-md-5 {{ $errors->has('password_confirmation') ? ' has-error' : '' }} ">
                            {{-- <label for="password_confirmation" class="col-md-4 control-label">{{__('header.Confirm Password')}}*</label> --}}

                            <input style="" placeholder="{{__('header.Confirm Password')}}" id="password_confirmation" type="password" class="form-control input-h-45 border-radius-2 color-grey-new" name="password_confirmation">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <fieldset class="companyinfo pad-14">
                        <div class="row pad-14">
                            <legend>{{__('header.Company Information')}}</legend>
                            <div class="col-md-6">
                                <div id="compName" class="form-group {{ $errors->has('companyName') ? ' has-error' : '' }}">
                                    {{-- <label for="companyName" class="col-md-4 control-label">{{__('header.Company Name')}}</label> --}}

                                    <input placeholder="{{__('header.Company name')}}" id="companyName" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="companyName" value="{{ old('companyName') }}" autofocus>

                                    @if ($errors->has('companyName'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('companyName') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                                <div id="compRegNo" class="form-group {{ $errors->has('companyRegNumber') ? ' has-error' : '' }}">
                                    {{-- <label for="companyRegNumber" class="col-md-4 control-label">{{__('header.Company Register No')}}*</label> --}}

                                    <input placeholder="{{__('header.Company register no')}}" id="companyRegNumber" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="companyRegNumber" value="{{ old('companyRegNumber') }}" autofocus>

                                    @if ($errors->has('companyRegNumber'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('companyRegNumber') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                                <div id="compAdd" class="form-group {{ $errors->has('companyAddress') ? ' has-error' : '' }}">
                                    {{-- <label for="companyAddress" class="col-md-4 control-label">{{__('header.Company Address')}}*</label> --}}

                                    <input placeholder="{{__('header.Address Line 1')}}" id="companyAddress" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="companyAddress" value="{{ old('companyAddress') }}" autofocus>

                                    @if ($errors->has('companyAddress'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('companyAddress') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                                <div id="compAdd2" class="form-group {{ $errors->has('companyAddress') ? ' has-error' : '' }}">
                                    {{-- <label for="companyAddress" class="col-md-4 control-label">{{__('header.Company Address')}}*</label> --}}

                                    <input placeholder="{{__('header.Address Line 2')}}" id="companyAddress2" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="companyAddress2" value="{{ old('companyAddress2') }}" autofocus>

                                    @if ($errors->has('companyAddress'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('companyAddress') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md-3 margin-right-48">
                                        <div class="form-group {{ $errors->has('postcode') ? ' has-error' : '' }}">
                                            <input id="postcode" type="text" class="form-control input-h-45 border-radius-2 text-grey" placeholder="Postcode" name="postcode" value="{{ old('postcode') }}" autofocus>
                                            @if ($errors->has('postcode'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('postcode') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                                            <input id="city" type="text" class="form-control input-h-45 border-radius-2 text-grey" placeholder="City" name="city" value="{{ old('city') }}" autofocus>
                                            @if ($errors->has('city'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('city') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 margin-right-48">
                                        <div class="form-group">
                                            <div id="state" class="padding-bottom-8 {{ $errors->has('state') ? ' has-error' : '' }}">
                                                {{Form::select('state', $locations, $stateid, ['class'=>'form-control input-h-45 border-radius-2 text-grey','placeholder'=> __('header.State')], [])}}
                                                @if ($errors->has('state'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('state') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <select name="country" class="form-control input-h-45 border-radius-2 text-grey">
                                                <option>{{__('header.Country')}}</option>
                                                <option>{{__('header.Malaysia')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="form-group border-radius-2" style="position: relative;">
                                    <span id="rosCertId" class="text-green line-hight">{{__('header.ROS Certificate')}}</span>
                                    <i class="glyphicon glyphicon-plus text-right text-green plus"></i>
                                    <input id="rosCertificate" type="file" name="rosCertificate" class="form-control plus-input" />
                                    @if ($errors->has('rosCertificate'))
                                        <span class="clearfix text-danger">
                                            <strong>{{ $errors->first('rosCertificate') }}</strong>
                                        </span>
                                    @endif
                                </div> --}}
                                <div id="compPhnNo" class="form-group {{ $errors->has('companyPhone') ? ' has-error' : '' }}">
                                    {{-- <label for="companyPhone" class="col-md-4 control-label">{{__('header.Company Phone')}}*</label> --}}

                                    <input placeholder="{{__('header.Company phone')}}" id="companyPhone" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="companyPhone" value="{{ old('companyPhone') }}" autofocus>

                                    @if ($errors->has('companyPhone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('companyPhone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div id="compFax" class="form-group {{ $errors->has('companyFax') ? ' has-error' : '' }}">
                                    {{-- <label for="companyFax" class="col-md-4 control-label">{{__('header.Company Fax')}}*</label> --}}

                                    <input placeholder="{{__('header.Company fax')}}" id="companyFax" type="text" class="form-control input-h-45 border-radius-2 color-grey-new" name="companyFax" value="{{ old('companyFax') }}" autofocus>

                                    @if ($errors->has('companyFax'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('companyFax') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="row top-bottom-40">
                                    <div class="col-md-12">
                                        <div id="ngoDon" class="form-group {{ $errors->has('ngoDonation') ? ' has-error' : '' }}">
                                            <label style="line-height: 22px;padding-right: 58px;color: #424242;" for="ngoDonation">{{__('header.Want Donations')}}</label>&nbsp;
                                            <input id="ngoDonation" type="radio" name="ngoDonation[]" value="1" autofocus><label style="color: #424242;">Yes</label>
                                            <input id="ngoDonationDont" type="radio" name="ngoDonation[]" value="0" autofocus checked><label style="color: #424242;">No</label>
                                            @if ($errors->has('ngoDonation'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('ngoDonation') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--20181025 Change by SHC 20180816_CP_002 start -->
                            <div class="col-md-3 text-left" style="padding-right:16px">
                                <label style="color:#424242; font-size: 13px; ">{{$certImageText}}</label>
                                <div style="max-width: 185px;height: 230px; border: 3px solid #000000;">
                                    <img src="{{$imageRef}}" class="img-ico" style="width: 100%;height: 100%;object-fit: contain;object-position: center center;" />
                                </div>
                                <span style="color: #8B0304; font-size: 10px;">Permitted files: PDF, JPG, PNG only</span>
                                <input id="rosCertificate" name="rosCertificate" type="file" accept="image/png, image/jpeg, image/bmp, image/gif, image/jpg, .pdf" class="file-upload" style="visibility: hidden"  />
                                @if ($errors->has('rosCertificate'))
                                    <span class="clearfix text-danger">
                                        <strong>{{ $errors->first('rosCertificate') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-3  text-left" style="padding-right:16px;">
                                <label style="color:#424242; font-size: 13px; ">{{$certImageText}}</label>
                                <div style="max-width: 185px;height: 230px; border: 3px solid #000000;">
                                    <img src="{{$imageRef}}" class="img-ico2" style="width: 100%;height: 100%;object-fit: contain;object-position: center center;" />
                                </div>
                                <span style="color: #8B0304; font-size: 10px;">Permitted files: PDF, JPG, PNG only</span>
                                <input id="rosCertificate2" name="rosCertificate2" type="file" accept="image/png, image/jpeg, image/bmp, image/gif, image/jpg, .pdf" class="file-upload" style="visibility: hidden;" />
                                @if ($errors->has('rosCertificate2'))
                                    <span class="clearfix text-danger">
                                        <strong>{{ $errors->first('rosCertificate2') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-3  text-left" style="padding-right:16px;">
                                <label style="color:#424242; font-size: 13px;">{{$certImageText}}</label>
                                <div style="max-width: 185px;height: 230px; border: 3px solid #000000;">
                                    <img src="{{$imageRef}}" class="img-ico3" style="width: 100%;height: 100%;object-fit: contain;object-position: center center;" />
                                </div>
                                <span style="color: #8B0304; font-size: 10px;">Permitted files: PDF, JPG, PNG only</span>
                                <input id="rosCertificate3" name="rosCertificate3" type="file" accept="image/png, image/jpeg, image/bmp, image/gif, image/jpg, .pdf" class="file-upload" style="visibility: hidden;"  />
                                @if ($errors->has('rosCertificate3'))
                                    <span class="clearfix text-danger">
                                        <strong>{{ $errors->first('rosCertificate3') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--20181025 Change by SHC 20180816_CP_002 End -->
                        </div>
                    </fieldset>


                    <div class="clearfix"></div>
                    <div class="row submitButtons" style="display: none;">
                            <div class="col-md-12 padding-left-30 padding-bottom-20">
                                <input id="agreebtn" type="radio" name="agree" value="1" style="margin-right: 6px;" />
                                <label for="agree" style="color: #424242;">{{__('header.agree')}}</label>
                                <a href="{{route('termsAndConditions')}}" class="text-maroon"><strong>{{__('header.Terms and Conditions')}}</strong></a>
                                {{-- <label for="ofctp">{{__('header.of CTP')}}</label> --}}
                            </div>
                        <div class="col-md-6" style="margin-top:30px">
                            <button id="signupbutton" type="submit" class="btn btn-md btn-primary margin-left-13" disabled="disabled">
                                <i class="fa fa-btn fa-user"></i>{{__('header.Sign Up')}}
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-md btn-primary">
                                {{__('header.Cancel')}}
                            </a>
                        </div>
                        <div class="col-md-12" style="margin-bottom:30px;">
                            <h6 class="already-signin" style="color: #424242;">{{__('header.Already registered')}}? {{__('header.Sign In')}}<a href="" data-toggle="modal" data-target="#loginModal" class="text-maroon"> {{__('header.here')}}!</a></h6>
                        </div>
                    </div>
                </form>
            </div>
                                </div>



        </div>
    </div>

    {{-- Tips popup --}}

    <div class="modal fade" id="tipsModal" role="dialog">
        <div class="modal-dialog modal-lg model-auto">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 0px !important;padding-bottom: 0px;padding-top: 8px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row" style="padding-bottom: 12px;">
                        <div class="col-md-12 text-center">
                            <div class="col-md-4">
                                <div style="border: 1px solid #000000; padding: 0px 20px;">
                                    <img src="{{ asset('/images/individual_ico.png')}}" style="background-repeat: no-repeat;background-position: center center;margin-top: 26px;">
                                    <div class="row" style="margin-top: 16px;">
                                        <span class="text-maroon" style="font-size: 18px;font-weight: bold;">{{__('header.INDIVIDUAL SELLER')}}</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>For individual users who are looking to advertise and sell products and items on cilipadi.com.my.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>Individual Seller account users are able to create posts to advertise their products and items for sale.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>Individual Seller account users enjoy </span><span style="font-weight:bold;">ONE (1) FREE POST</span><span> at any one time!</span>
                                    </div>
                                    <div class="row" style="height: 147px;">
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span class="text-maroon" style="font-size: 14px; font-weight: bold;">Why Create Individual Seller Account?</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Create posts to advertise their products and items for sale</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Enjoy </span><span style="font-weight: bold;">ONE (1) FREE POST</span><span> at any one time!</br></span>
                                    </div>
                                    <div class="row" style="height: 46px;">
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px; margin-bottom: 15px;">
                                        <button type="button" class="btn btn-md btn-white-tips ind-open">Create Individual Seller Account Now!</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="border: 1px solid #000000; padding: 0px 20px;">
                                    <img src="{{ asset('/images/corporate_ico.png')}}" style="background-repeat: no-repeat;background-position: center center;margin-top: 26px;">
                                    <div class="row" style="margin-top: 16px;">
                                        <span class="text-maroon" style="font-size: 18px;font-weight: bold;">{{__('header.CORPORATE SELLER')}}</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>For registered Sdn Bhd or companies who are looking to create an online presence for their businesses.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>Corporate Seller account users are able to create their own online store within cilipadi.com.my to showcase all their products and items for sale.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>The added management tool enables Corporate Seller account users to create, edit, delete, publish and unpublish posts at any time, giving better control and management.</span>
                                    </div>
                                    <div class="row" style="height: 82px;">
                                    </div>
                                    <div class="row" style="margin-top: 15px;">
                                        <span class="text-maroon" style="font-size: 14px; font-weight: bold;">Why Create Corporate Seller Account?</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>User is a registered Sdn Bhd or company</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Create online store within cilipadi.com.my</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Management tool</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Purchase ads</br></span>
                                    </div>
                                    <div class="row" style="height: 19px;">
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;margin-bottom: 15px;">
                                        <button type="button" class="btn btn-md btn-white-tips cor-open">Create Corporate Seller Account Now!</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="border: 1px solid #000000; padding: 0px 20px;">
                                    <img src="{{ asset('/images/ngo_ico.png')}}" style="background-repeat: no-repeat;background-position: center center;margin-top: 26px;">
                                    <div class="row" style="margin-top: 16px;">
                                        <span class="text-maroon" style="font-size: 18px;font-weight: bold;">{{__('header.NGO')}}</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>For registered NGOs, charity or non-profit organizations who are looking to create an online presence for their organizations.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>NGOs account users are able to create their own online store within cilipadi.com.my to showcase all their products and items for sale.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>The added management tool enables NGO account users to create, edit, delete, publish and unpublish posts at any time, giving better control and management.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span>NGO account users are also able to receive credit donations from other cilipadi.com.my users. Credits received through donations may be used within cilipadi.com.my or converted to cash donations.</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;">
                                        <span class="text-maroon" style="font-size: 14px; font-weight: bold;">Why Create An NGO Account?</span>
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;">
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>User is a registered NGO, charity or non-profit organization</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Create online store within cilipadi.com.my</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Management tool</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Purchase ads</br></span>
                                        <span class="glyphicon glyphicon-ok" style="font-size: 11px;">&nbsp;</span><span>Recevive credit donations</br></span>
                                    </div>
                                    <div class="row" style="height: 5px;">
                                    </div>
                                    <div class="row" style="margin-top: 15px;color: #000000;font-size: 13px;margin-bottom: 15px;">
                                        <button type="button" class="btn btn-md btn-white-tips ngo-open">Create NGO Account Now!</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tips popup END --}}

    <script type="text/javascript">

        function showfields(typeid){
            var x = typeid;
            if(x == 0)
            {
                $('#hngo').show();
                $('.NgoCatInfo').show();
                $('.fb-area').hide();
                $('.AccInfo').show();
            }
            if(x == 2) {
                var translatedString = '{{__("header.Delete")}}';
                document.getElementById("username").innerHTML = "{{__('header.Store Name Username')}}";
                document.getElementById('first_name').placeholder = "{{__('header.Store Name Username')}}";
                //$('#stname').show();
                //$('#nkname').hide();
                $('#compName').show();
                $('#compRegNo').show();
                $('#compAdd').show();
                $('#compPhnNo').show();
                $('#compFax').show();
                $('#RosCert').show();
                $('#myDIV').show();
                $('.fb-area').hide();
                $('#hngo').hide();
                $('.NgoCatInfo').hide();
                $('.AccInfo').show();
                if(x == 0)
                {
                    $('#ngoDon').show();
                    $('#ngoCat').show();
                    $('#hngo').show();
                    $('.NgoCatInfo').show();
                }
                else
                {
                    $('#ngoDon').hide();
                    $('#ngoCat').hide();
                    $('#hngo').hide();
                    $('.NgoCatInfo').hide();
                    $("input[name='ngoDonation']").val(0);
                }
            }
            else
            {
                document.getElementById("username").innerHTML = "{{__('header.Username')}}";
                document.getElementById('first_name').placeholder = "{{__('header.Username')}}";
                //$('#nkname').show();
                //$('#stname').hide();
                $('#compName').hide();
                $('#compRegNo').hide();
                $('#compAdd').hide();
                $('#compPhnNo').hide();
                $('#compFax').hide();
                $('#RosCert').hide();
                $('#myDIV').hide();
                $('#ngoDon').hide();
                $('#ngoCat').hide();
                $('.fb-area').show();
                $('#hngo').hide();
                $('.NgoCatInfo').hide();
                $('.AccInfo').hide();
                document.getElementById("companyName").value = "";
                document.getElementById("companyRegNumber").value = "";
                document.getElementById("companyAddress").value = "";
                document.getElementById("companyPhone").value = "";
                document.getElementById("companyFax").value = "";
                document.getElementById("companyFax").value = "";
                $("input[name='ngoDonation']").val(0);
                //document.getElementById("rosCertificate").value = "";
            }if(x == 0 || x == 2) {
                var translatedString = '{{__("header.Delete")}}';
                document.getElementById("username").innerHTML = "{{__('header.Store Name Username')}}";
                document.getElementById('first_name').placeholder = "{{__('header.Store Name Username')}}";
                //$('#stname').show();
                //$('#nkname').hide();
                $('#compName').show();
                $('#compRegNo').show();
                $('#compAdd').show();
                $('#compPhnNo').show();
                $('#compFax').show();
                $('#RosCert').show();
                $('#myDIV').show();
                $('.fb-area').hide();
                $('.AccInfo').show();
                if(x == 0)
                {
                    $('#ngoDon').show();
                    $('#ngoCat').show();
                    $('#hngo').show();
                    $('.NgoCatInfo').show();
                }
                else
                {
                    $('#ngoDon').hide();
                    $('#ngoCat').hide();
                    $('#hngo').hide();
                    $('.NgoCatInfo').hide();
                    $("input[name='ngoDonation']").val(0);
                }
            }
            else
            {
                document.getElementById("username").innerHTML = "{{__('header.Username')}}";
                document.getElementById('first_name').placeholder = "{{__('header.Username')}}";
                //$('#nkname').show();
                //$('#stname').hide();
                $('#compName').hide();
                $('#compRegNo').hide();
                $('#compAdd').hide();
                $('#compPhnNo').hide();
                $('#compFax').hide();
                $('#RosCert').hide();
                $('#myDIV').hide();
                $('#ngoDon').hide();
                $('#ngoCat').hide();
                $('.fb-area').show();
                $('#hngo').hide();
                $('.NgoCatInfo').hide();
                $('.AccInfo').hide();
                document.getElementById("companyName").value = "";
                document.getElementById("companyRegNumber").value = "";
                document.getElementById("companyAddress").value = "";
                document.getElementById("companyPhone").value = "";
                document.getElementById("companyFax").value = "";
                document.getElementById("companyFax").value = "";
                $("input[name='ngoDonation']").val(0);
                //document.getElementById("rosCertificate").value = "";
            }
        }

        $(document).ready(function (){

            //$('#phone_number, #companyPhone, #companyFax').inputmask({ regex: "\\d{0,11}" });
            $('#postcode').inputmask({ regex: "\\d{0,5}" });
            $('#phone_number').inputmask({"mask": "999-99999999"});
            $('#companyPhone, #companyFax').inputmask({"mask": "99-99999999"});
           //20181025 Change by SHC 20180816_CP_001 - Start
            //$('#first_name').inputmask({regex: "[a-zA-Z-_]+", "placeholder": ""}); 
            $('#first_name').inputmask({regex: "[a-zA-Z-_0-9]+", "placeholder": ""}); 
            $('#email').inputmask("email"); //20181025 Change by SHC 20180816_CP_001
            $('#companyName').inputmask({regex: "[a-zA-Z0-9 ]+", "placeholder": ""}); 
            //20181025 Change by SHC 20180816_CP_001 - End 
            var userType = '{{$typeSelection}}';
            // $('#'+userType).trigger('click');
            if(userType != '')
            {
                $('.'+userType).trigger('click');
            }

            $('.img-ico').click(function(){
                $('#rosCertificate').trigger('click');
            });

            //20181025 Change by SHC 20180816_CP_002 - Start
            $('.img-ico2').click(function(){
                $('#rosCertificate2').trigger('click');
            });

            $('.img-ico3').click(function(){
                $('#rosCertificate3').trigger('click');
            });
            //20181025 Change by SHC 20180816_CP_002 - End
            $('#agreebtn').click(function(){
                $('#signupbutton').removeAttr('disabled');
            });


            $(document).on('change', '#rosCertificate', function()
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

                    //20181025 Change by SHC 20180816_CP_002 - Start
                    else
                    {
                        if (fileObj[0].files[0] && (fileObj[0].files[0].type == "application/pdf")){

                        var reader = new FileReader();
                        reader.onload = function (e)
                        {
                            var target = e.target || e.srcElement;
                            $(imageObject)
                                .attr('src',  'images/PDF.png')
                                .css('width', '100%');
                        }
                        reader.readAsDataURL(fileObj[0].files[0]);
                        $(imageObject).attr('title', fileObj[0].files[0].name);
                        }
                    //20181025 Change by SHC 20180816_CP_002 - End
                    else
                    {
                        $(imageObject).attr('title', "");
                    }
                }
            }
            });

            //20181025 Change by SHC 20180816_CP_002 - Start
            $(document).on('change', '#rosCertificate2', function()
            {
                var fileObject = $(this);
                var imageObject = $('.img-ico2');

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
                        if (fileObj[0].files[0] && (fileObj[0].files[0].type == "application/pdf")){

                        var reader = new FileReader();
                        reader.onload = function (e)
                        {
                            var target = e.target || e.srcElement;
                            $(imageObject)
                                .attr('src',  'images/PDF.png')
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
                }
            });
            $(document).on('change', '#rosCertificate3', function()
            {
                var fileObject = $(this);
                var imageObject = $('.img-ico3');

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
                        if (fileObj[0].files[0] && (fileObj[0].files[0].type == "application/pdf")){

                        var reader = new FileReader();
                        reader.onload = function (e)
                        {
                            var target = e.target || e.srcElement;
                            $(imageObject)
                                .attr('src',  'images/PDF.png')
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
                }
            });
            //20181025 Change by SHC 20180816_CP_002 - End
        });
        $(document).on('click','.radio',function(){
            var x = $(this).val();
            showfields(x);
        });

        $(document).on('click', '.ind-open', function(){
            $('#tipsModal').modal('hide');
            $('.individual').trigger('click');
        });

        $(document).on('click', '.cor-open', function(){
            $('#tipsModal').modal('hide');
            $('.corporate').trigger('click');
        });

        $(document).on('click', '.ngo-open', function(){
            $('#tipsModal').modal('hide');
            $('.ngo').trigger('click');
        });


        $(document).on('click', '.individual', function(){
            $('#postErrorBox ul').html('');
            $("html, .container").animate({ scrollTop: 0 }, 600);
            $('.companyinfo').removeClass('active');
            $('.corporate').removeClass('active');
            $('.ngo').removeClass('active');

            $(this).addClass('active');
            $('#1st-ind').show();
            $('.submitButtons').show();
            $('#individual').prop('checked',true);
            showfields($('#individual').val());
        });

        $(document).on('click', '.corporate', function(){
            $('#postErrorBox ul').html('');
            $("html, .container").animate({ scrollTop: 0 }, 600);
            $('.individual').removeClass('active');
            $('.ngo').removeClass('active');
            $(this).addClass('active');
            $('.companyinfo').addClass('active');
            $('#1st-ind').show();
            $('.submitButtons').show();
            $('#corporate').prop('checked', true);
            showfields($('#corporate').val());
        });

        $(document).on('click', '.ngo', function(){
            $('#postErrorBox ul').html('');
            $("html, .container").animate({ scrollTop: 0 }, 600);
            $('.individual').removeClass('active');
            $('.corporate').removeClass('active');
            $(this).addClass('active');
            $('.companyinfo').addClass('active');
            $('#1st-ind').show();
            $('.submitButtons').show();
            $('#ngo').attr('checked',true);
            showfields($('#ngo').val());
        });

        $(document).on('click','#signupbutton',function(e){
            e.preventDefault();

            $('#registerfrm').ajaxForm(options);
            $('#registerfrm').submit();
        });

        var options = { 
            complete: function(response) 
            {
              if($.isEmptyObject(response.responseJSON.registerationError)){
                window.location.href = '{{URL::to('registerDone')}}';
              }else{
                var text = "";
                $.each(response.responseJSON.registerationError[0], function(index, value){
                  text += '<li class="alert alert-danger">'+value+'</li>';
                });
                $('#postErrorBox ul').html(text);
                $('#postErrorBox').show();

                window.scrollTo(0, 0);
              }
            }
        };

    </script>
@endsection
