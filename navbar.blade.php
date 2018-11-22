<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid margin-right-15">
        <div class="collapse navbar-collapse" id="nav-main-menu">
            <div class="navbar-nav nav navbar-right">
                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="dropdown-profile">
                        @include('laravel-authentication-acl::admin.layouts.partials.avatar', ['size' => 30])
                        <span id="nav-email">{!! isset($logged_user) ? $logged_user->email : 'User' !!}</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                             <!-- 20181114 Changed by SHC PL:20180816_CP_005 add your profile link-->
                            <a href="{!! URL::route('users.profile.edit',['user_id' => $logged_user]) !!}"><i class="fa fa-user"></i> Your profile</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{!! URL::route('user.logout') !!}"><i class="fa fa-sign-out"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </div>

            <ul class="nav navbar-nav">
                @if(isset($menu_items))
                @foreach($menu_items as $item)
                <li class="{!! LaravelAcl\Library\Views\Helper::get_active_route_name($item->getRoute()) !!}"> <a href="{!! $item->getLink() !!}">{!!$item->getName()!!}</a></li>
                @endforeach
                @endif
                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span id="nav-email">User Approval</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/admin/individual"><i class="fa fa-user"></i> Individual</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/ngo"><i class="fa fa-user"></i> Ngo</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/corporate"><i class="fa fa-user"></i> Corporate</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span id="nav-email">Settings</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/admin/packages"> Package Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/categories"> Category Settings</a>
                        </li>
                        <li class="divider"></li>
                         <li>
                            <a href="/admin/banners"> Ads Package Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/editFreePost"> Free Post</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/setExpirePost"> Expire Post</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/editAdSpace"> Ad Space Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/setUserStatement"> Statement Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/ngoCategorySettings"> Ngo Category Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/addVoucher"> Add Voucher</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span id="nav-email">Approval</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                         <li>
                            <a href="/admin/pendingTransactions">Transactions </a>
                        </li>
                        
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/ads"> Ads Approval</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/donation"> Donation Approval</a>
                        </li>
                         <li class="divider"></li>
                        <li>
                            <a href="/admin/bannersapproval"> Banner Transaction</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{route('banner.approval')}}"> Banner Approval</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/liveAds"> Live Ads</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span id="nav-email">Complaints</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/admin/usercomplain">General </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/complain"> Ads</a>
                        </li>

                    </ul>
                </li>

                <li>
                    <a href="/admin/Convert"> Credits Request</a>
                </li>

                <li>
                    <a href="/admin/searchPost"> Search Ads</a>
                </li>


                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span id="nav-email">Reports</span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                       
                        <li>
                            <a href="/admin/reports"> Audit Reports</a>
                        </li>
                        
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/UserReports">User Report</a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="/admin/creditDebitedReport">Credit Debited Report</a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="/admin/creditCreditedReport">Credit Credited Report</a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="/admin/premiumFunctionReport">Premium Function Report</a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="/admin/userStatementReport">User Statement Report</a>
                        </li>

                         <li class="divider"></li>
                        <li>
                            <a href="/admin/PurchaseReports">Purchase Credit Report</a>
                        </li>
                        
                        <li class="divider"></li>
                        <li>
                            <a href="/admin/ConvertReports">Convert Credit Report</a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="/admin/PostComplaintReports">Complaint Seller Post Report</a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="/admin/ComplaintReports">Complaint Report</a>
                        </li>
                       
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>