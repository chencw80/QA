@php
  $currentUrl = '';
  $currentUrl = request()->input('ref');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>


  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="icon" type="image/png" href="{{ asset('cilipadi_icon.ico') }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Styles -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet" />

  {!! HTML::style('packages/jacopo/laravel-authentication-acl/css/bootstrap.min.css') !!}
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/navbar-fixed-side.css') }}" rel="stylesheet">
  <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet" />

  <link href="{{ asset('/js/datepicker/dist/datepicker.css') }}" rel="stylesheet" />
  <style>
  #radioButtons label{
    margin-right : 40px;
  }
</style>

<link href="{{ asset('css/jquery.bxslider.css') }}" rel="stylesheet" />
<link href="{{ asset('icheck/skins/all.css') }}" rel="stylesheet" />
<link href="{{ asset('slick/slick.css') }}" rel="stylesheet" />
<link href="{{ asset('slick/slick-theme.css') }}" rel="stylesheet" />


<link href="{{ URL::to('selectric/selectric.css') }}" rel="stylesheet" />
<link href="{{ asset('css/custom.css') }}" rel="stylesheet" />

<script src="{{ asset('js/jquery.min.js') }}" type="text/javascript"></script>
  <style>
    .nav-tabs.wizard {
      background-color: transparent;
      padding: 0;
      width: 60%;
      /*margin: 1em auto;*/
      border-radius: .25em;
      clear: both;
      border-bottom: none;
    }

    .nav-tabs.wizard li {
      width: 100%;
      float: none;
      margin-bottom: 3px;
    }

    .nav-tabs.wizard li a:first-child
    {
      border-bottom-left-radius: 4px;
    }
    .nav-tabs.wizard li>* {
      position: relative;
      padding: 1em .8em .8em 2.5em;
      color: #ffffff;
      background-color: #58595b;
      border-color: #58595b;
    }

    .nav-tabs.wizard li.completed>* {
      color: #fff !important;
      background-color: #96c03d !important;
      border-color: #96c03d !important;
      border-bottom: none !important;
    }

    .nav-tabs.wizard li.active>* {
      color: #fff !important;
      background-color: #8B0304 !important;
      border-color: #8B0304 !important;
      border-bottom: none !important;
    }

    .nav-tabs.wizard li::after:last-child {
      border: none;
    }

    .nav-tabs.wizard > li > a {
      opacity: 1;
      font-size: 13px;
      text-align: center;
    }

    .nav-tabs.wizard a:hover {
      color: #fff;
      background-color: #8B0304;
      border-color: #8B0304;
    }
    @media(min-width:992px) {
      .nav-tabs.wizard li {
        position: relative;
        padding: 0;
        margin: 4px 4px 4px 0;
        width: 19.6%;
        float: left;
        text-align: center;
      }

      .nav-tabs.wizard li.active a {
        padding-top: 15px;
      }

      .nav-tabs.wizard li::after,
      .nav-tabs.wizard li > *::after {
        content: '';
        position: absolute;
        top: 1px;
        left: 100%;
        height: 0;
        width: 0;
        border: 29px solid transparent;
        border-right-width: 0;
      }

      .nav-tabs.wizard li::after {
        z-index: 1;
        -webkit-transform: translateX(4px);
        -moz-transform: translateX(4px);
        -ms-transform: translateX(4px);
        -o-transform: translateX(4px);
        transform: translateX(4px);
        border-left-color: #f5f5f5;
        margin: 0
      }

      .nav-tabs.wizard li > *::after {
        z-index: 2;
        border-left-color: inherit
      }
    }
  </style>
</head>
<body>
  <div id="app" >


  <!-- Start of cili header   -->
  <div class="cili-header">
    <nav class="navbar navbar-default navbar-static-top header"> <!-- 20181011 Added by SHC MIN-HEIGHT-->

      <div class="container" >
        <div class="row">
          <div class="navbar-header cili-navbar-header" >
            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle cili-navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
              <span class="sr-only">Toggle Navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand cili-brand" href="{{ url('/') }}">
              <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" />
            </a>
          </div>

          <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <div class="clearfix navbar-clearfix">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right cili-left-side-navbar">
              {{-- <li><a href="/">{{ __('header.Home')}}</a></li> --}}
              {{-- <li> <a href="{{ route('Complaint') }}">{{__('header.Complain')}}</a></li> --}}
              <li> <a href="{{ route('viewAllNgos') }}">{{__('header.View NGOs')}}</a></li>
              @if (Auth::guest())
                <li> <a href="{{ route('makeDonationNgos') }}">{{__('header.Make Donation')}}</a></li>
              @else
                @if(Auth::user()->type == 1 || Auth::user()->type == 2)
                  <li> <a href="{{ route('makeDonationNgos') }}">{{__('header.Make Donation')}}</a></li>
                @endif
              @endif

              @if (Auth::guest())
                <li><a href="#" data-toggle="modal" data-target="#loginModal">SIGNUP/LOGIN</a></li>
              @endif

              @if (!Auth::guest())
              {{-- <li><a>{{__('header.Credits:') }} {{$check = (Auth::user()->user->credits > 0)?Auth::user()->user->credits:0}} </a></li> --}}
              @endif
              @if (!Auth::guest() && Auth::user()->type == 1)
              {{-- <li><a>{{__('header.Free Posts') }}: {{$freePosts = (Auth::user()->user->free_post > 0)?Auth::user()->user->free_post:0}} </a></li> --}}
              @endif
              @if (!Auth::guest() && Auth::user()->user->account_type == 0)
              {{-- <li><a>{{__('header.Donations') }}: {{$donations = (Auth::user()->user->donatedCredits > 0)?Auth::user()->user->donatedCredits:0}} </a></li> --}}
              @endif

              @if (Auth::guest())
              {{-- <li><a href="{{ route('login') }}">{{ __('header.Login')}}</a></li> --}}
              {{-- <li><a href="{{ route('register') }}" data-toggle="tooltip" rel="tooltip" data-placement="top" title="To post an Ad please register">{{__('header.Register')}}?</a></li> --}}
              @else
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                {{-- <a href="{{route('showDashboard')}}"> --}}
                  
                  <!-- 20181120 remark by SHC PL:20181119_CP_023
                  @if(Auth::user()->image!=null)
                  <img src="{{ asset('profile_image/' . Auth::user()->image) }}" width="25" height="25" />
                  @else
                  <img src="{{ asset('profile_image/default_image.png') }}" width="10" height="10" />
                  @endif-->

                  {{ Auth::user()->name}} <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li>
                    <a href="{{ route('showDashboard') }}">
                      {{__('header.Dashboard')}}
                    </a>
                    <a href="{{ route('ContactUs') }}">
                      {{__('header.Contact Us')}}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      {{csrf_field()}}
                    </form>
              
                    <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    {{__('header.Logout')}}
                    </a>
                  </li>
                </ul>
              </li>
            @endif
              {{-- <li><a href="{{ route('wishlist') }}">{{ __('header.WishList') }}</a></li> --}}
              <li><a href="{{ route('wishlist') }}"><img height="10px" src="{{ asset('images/wishlist_menu_icon.png') }}" alt="{{ __('header.WishList') }}"></a></li>
              <!-- 20181012 Remark by SHC move create post to search session
              <li class="post_free_ads">
                @if(!Auth::guest())
                  @if(Auth::user()->type == 1)
                    @if(Auth::user()->user->free_post > 0)
                      <a href="{{ route('postCreation', ['scId' => 1, 'id' => 0]) }}" style="font-weight: bold;"> {{__('header.Create Free Post')}}</a>
                    @else
                      <a href="{{ route('postCreation', ['scId' => 1, 'id' => 0]) }}" style="font-weight: bold;"> {{__('header.Create Post')}}</a>
                    @endif
                  @else
                    <a href="{{ route('postCreation', ['scId' => 1, 'id' => 0]) }}" style="font-weight: bold;">{{ __('header.Create Post')}}</a>
                  @endif
                @else
                  <a href="#" data-toggle="modal" data-target="#loginModal" style="font-weight: bold;">{{ __('header.Create Free Post')}}</a>
                @endif
              </li>-->

              <!-- <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                  {{ __('header.Languages')}}
                  <span class="caret"></span>
                </a>

                <ul class="dropdown-menu">
                  <?php if ( !\Session::has('locale') )
                  {
                    \Session::put('locale', \Config::get('app.locale'));
                  }
                  ?>
                  <li>
                    <a href="{{URL::to('/language/change/to/en')}}">English</a>
                  </li>
                  <li>
                    <a href="{{URL::to('/language/change/to/Malay')}}">Malay</a>
                  </li>
                </ul>
              </li> -->
            </ul>
                </div>
            @yield('contentSearch')<!-- 20181011 Added by SHC move search keyword session to SearchTop-->
          </div>
        </div>
      </div>
    </nav>
  </div>
  <!-- End of cili header   -->

  @if(request()->is('/') || request()->is('home'))
    <div id="slider" class="home-slider">
      @if($banners->count() > 0)
        @foreach($banners as $banner)
          <div class="cili-banner" style="background-image: url({{ asset('images/'.$banner->banner_path) }});">
            <a href="{{ ('/store/'.$banner['getUser'][0]['first_name']) }}">
            <img src="{{ asset('images/trans.png') }}" alt="" style="width: 100%;height: 100%;object-fit: contain;object-position: center center;"/>
            </a>
          </div>
        @endforeach
      @else
          <img src="{{ asset('images/slider-banner-1.png') }}" alt="" />
      @endif
    </div>
  @endif

  <div class="container">
    <div class="row">
      @if($status = Session::get('status'))
        <div class="text-center alert alert-danger">{{$status}}</div>
      @endif

      @if($status = Session::get('success'))
        <div class="text-center alert alert-success">{!! $status !!}</div>
      @endif

    </div>
  </div>
    @yield('content')

    <div class="modal fade" id="loginModal" role="dialog">
      <div class="modal-dialog modal-lg model-auto">
         <!-- Modal content-->
         <div class="modal-content bg-login">
            <div class="modal-body">
               @include('auth.login')
            </div>
         </div>
      </div>
    </div>

  <!-- End of App -->
  </div>


{{-- Ad Section --}}
{{-- @include('layouts.ad_banner') --}}
{{-- end Ad section --}}

@if(isset($smallFooter) && $smallFooter == true)
  <div id="smallFooter" style="padding: 15px;">
    @include('layouts.smallFooter')
  </div>
@else
  <div id="footer">
    @include('layouts.footer')
  </div>
  <div id="footerLogo">
    @include('layouts.footerLogo')
  </div>
@endif





<!-- Scripts -->

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/jquery.bxslider.min.js') }}"></script>
<script src="{{ asset('/js/datepicker/dist/datepicker.js') }}" ></script>
<script src="{{ asset('/js/jquery.devrama.lazyload.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>

<script src="{{ asset('js/jqueryInputMask/inputmask/inputmask.js') }}"></script>
<script src="{{ asset('js/jqueryInputMask/inputmask/inputmask.extensions.js') }}"></script>
<script src="{{ asset('js/jqueryInputMask/inputmask/inputmask.numeric.extensions.js') }}"></script>
<script src="{{ asset('js/jqueryInputMask/inputmask/inputmask.date.extensions.js') }}"></script>
<script src="{{ asset('js/jqueryInputMask/inputmask/inputmask.phone.extensions.js') }}"></script>
<script src="{{ asset('js/jqueryInputMask/inputmask/jquery.inputmask.js') }}"></script>
<script src="{{ asset('icheck/icheck.js') }}"></script>
<script src="{{ asset('slick/slick.min.js') }}"></script>
<script src="{{ asset('selectric/jquery.selectric.min.js') }}"></script>
<script src="{{ asset('js/jquery.form.js') }}" type="text/javascript"></script>
{{-- <script src="{{ asset('js/jquery.fileuploader.js') }}" type="text/javascript"></script> --}}

@yield('additional_js')

  <script type="text/javascript">
    $(document).ready(function(){
        var aspForm  = $("form");
        var firstInput = $(":input:not(input[type=button],input[type=submit],button,input:disabled):visible:first", aspForm);
        //firstInput.focus();


        //$('[data-toggle="tooltip"]').tooltip({'placement': 'top'});
        // slider
        $('.home-slider').bxSlider({
          auto: true,
          autoControls: false,
          pager: true,
          speed: 1000,
        });

        @if($currentUrl == "login")
          $('#loginModal').modal();
        @endif

        // NGO Slider
        $('.ngo-slider').slick({
          autoplay: true,
          infinite: true,
          slidesToShow: 5,
          slidesToScroll: 3,
          autoplaySpeed: 5000,
        });

        // checkbox skins
        $('input[name="ngoDonation[]"],input[name="websiteLinks[]"],#postadTerms,.remember_me,#radioButton input[type="radio"], input[name="itemnegotiate"]').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red'
        });
    });
  </script>
</body>
</html>
