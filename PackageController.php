<?php

namespace App\Http\Controllers\Admin;
use App\AdminSetting;
use App\AppUser;
use App\ChequeDetail;
use App\FreePostModel;
use App\Http\Controllers\Controller;
use App\PackageModel;
use App\PaymentInfo;
use App\PurchaseHistory;
use App\AdsSetting;
use App\PackageBanner;
use App\VoucherModel;
use Auth;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use LaravelAcl\Authentication\Interfaces\AuthenticateInterface;
use Mail;
use Redirect;
use Session;
use URL;
use View, App, Config;
use \Illuminate\Config\FileLoader;

class PackageController extends Controller
{
    public function __construct(AuthenticateInterface $auth)
    {
      $this->auth = $auth;
    }

    public function getPackages()
    {
      $packages = PackageModel::where('enabled' , '<=' , 1)->get();
      return view('admin_package_view')->with('packages', $packages);
    }

    public function deletePackage($id)
    {
      PackageModel::where('id', $id)->update(['enabled' => 2]);
      $packages = PackageModel::where('enabled', 1)->get();
      return view('admin_package_view')->with('packages', $packages);
    }

    protected function validator(array $data)
    {
      $packageExistCount = 0;

      if(isset($data['price']) && isset($data['num_post']) && isset($data['validity']))
      {
        $packageExistCount = PackageModel::where(['price' => $data['price'], 'num_post' => $data['num_post'], 'validity' => $data['validity']])->where('enabled', '<>', 2)->count();
      }

      $arr = [
        "price.required"=> "Package price is required",
        "num_post.required"=> "Credits is required",
        "validity.required"=> "Package expiry is required",
        "price.digits_between"=> "Package price must be between 1 and 10 digits",
        "num_post.digits_between"=> "Credits must be between 1 and 10 digits",
        "num_post.unique"=> "Credits Package Must be Unique",
        "validity.digits_between"=> "Package must be between 1 and 10 digits",
        "packageName.required"=> "Package name is required",
        "packageName.unique"=> "Package name must be unique",
      ];

      $rules = [
        'price' => 'required|digits_between:1,10',
        'validity' => 'required|digits_between:1,10',
        'packageName' => 'required|string|max:255|regex:/^[a-z A-Z]+$/u|unique:package',
      ];

      if($packageExistCount > 0)
      {
        $rules['num_post'] = 'required|unique:package|digits_between:1,10'; 
      }
      else
      {
        $rules['num_post'] = 'required|digits_between:1,10';
      }

      return Validator::make($data, $rules, $arr);
    }

    public function addEditPackage(Request $req)
    {
      $input = $req->all();
      $validator = $this->validator($input);

      if($req->check == 0)
      {
        $array = array();
        $array = array_add($array, 'enabled', $req->status);
        $updated = PackageModel::where('id', $req->packageid)->update($array);
      }
      else
      {
        if($validator->passes())
        { 
          PackageModel::create([
          'price' => $req->price, 
          'post_type' => $req->input('posttype'), 
          'num_post' => $req->num_post, 
          'enabled' => $req->input('status'),
          "validity" => $req->input('validity'), 
          "packageName" => $req->input('packageName'),
          ]);
        }    
        else
        {
          $messages = $validator->messages();
          Session::flash('EmailError', $messages->all());
          return Redirect()->back()->withInput();
        }
      }
      $packages = PackageModel::where('enabled' , '<=' , 1)->get();
      return view('admin_package_view')->with('packages', $packages);
    }

    public function addEditview($id, $check)
    {
      $packages = [];
      
      if ($check == 0)
        $packages = PackageModel::where('id', $id)->get();

      return view('add_edit_package')->with('id', $id)->with('check', $check)->with('package', $packages);
    }

    public function editFreePost()
    {
      $freepost = FreePostModel::all();
      return view('free_post')->with('freepost' , $freepost);
    }

    public function editAdSpace()
    {
      $adsSetting = AdsSetting::all();
      return view('ads_space_setting')->with('adsSetting' , $adsSetting);
    }

    public function setAdsSpace(Request $req)
    {
      $adsSetting = AdsSetting::all();

      if(count($adsSetting)==0)
      {
        AdsSetting::create([
          'small_ads_count' => $req->smallCount,
          'medium_ads_count' => $req->mediumCount,
          'large_ads_count' => $req->largeCount,
        ]);
      }
      else
      {
        $updated = AdsSetting::where('id',1)->update(['large_ads_count' => $req->largeCount, 'medium_ads_count' => $req->mediumCount, 'small_ads_count' => $req->smallCount]);
      }

      return redirect('admin/editAdSpace')->with('status','Updated successfully');
    }

    public function setFreePost(Request $req)
    {
      $freepost = FreePostModel::all();

      if(count($freepost)==0)
      {
        FreePostModel::create([
          'free_post_no' => $req->freepost, 
          'price_post' => $req->input('price') , 
        ]);
      }
      else
      {
        $updated = FreePostModel::where('id',1)->update(['free_post_no' => $req->freepost , 'price_post' => $req->price]);
      }

      $freepost = FreePostModel::all();
      return redirect('admin/editFreePost')->with('status','Updated successfully');
    }

    public function approvedTransactions()
    {
      AppUser::expireCredits();//check credits expiry
      $sidebar = $this->sideMenu();
      $data = PurchaseHistory::where('status' , 1)->with('getPackage', 'getUser')->orderBy('created_at' , 'desc')->paginate(10);
      return view('creditTransactions')->with('data',$data)->with('flag', 1)->with('sidebar_items', $sidebar);
    }

    public function pendingTransactions()
    {
      AppUser::expireCredits();//check credits expiry
      $appUser = $this->auth->getLoggedUser();
      $sidebar = $this->sideMenu();
      $data = PurchaseHistory::where('status', 0)->where('first_approval','NOT LIKE','%'.$appUser->id.'%')->with('getPackage', 'getUser')->orderBy('created_at' , 'desc')->paginate(10);

      return view('creditTransactions')->with('data',$data)->with('flag', 0)->with('sidebar_items', $sidebar);
    }

    public function approvePendingTransactions(Request $req)
    {
      $appUser = $this->auth->getLoggedUser();
      AppUser::expireCredits();//check credits expiry
      $approve = PurchaseHistory::where('id', $req->id)->select('first_approval','sec_approval', 'user_id', 'package_id')->first();

      if($approve['first_approval'] == 0)
      {
        PurchaseHistory::where('id', $req->id)->update(['first_approval' => $appUser->id, 'first_approval_date' => Carbon::now()]);
      }
      elseif($approve['first_approval'] != 0)
      {
        PurchaseHistory::where('id', $req->id)->update(['sec_approval' => $appUser->id, 'status' => 1, 'sec_approval_date' => Carbon::now()]);

        PackageModel::where('id', $approve['package_id'])->update(['count'=> 1]);// incrementing package purchase count

        $packages = PackageModel::where('id', $approve['package_id'])->where('enabled', 1)->first();
        $userRecord = AppUser::where('user_id', $approve['user_id']);
        $userRecordGet = $userRecord->first();

        $userUpdateArray = [];

        $userUpdateArray["credits"] = $userRecordGet->credits + $packages["num_post"];
        
        // First Time Package Add
        if($userRecordGet->validity == 0)
        {
          $oldpackage = strtotime(Carbon::now()->addDays($packages["validity"]));
          $userUpdateArray["validity"] = $oldpackage;
        }
        else
        {
          // Second Time Add
          $perviousdate = Carbon::createFromTimestamp($userRecordGet->validity); 
          $saveDate = $perviousdate->addDays($packages["validity"]);
          $userUpdateArray["validity"] = strtotime($saveDate);
        }
        
        $userRecord->update($userUpdateArray);
        $userData = AppUser::where('user_id', $approve['user_id'])->select('email')->first();

        $data = array(
          'email' => $userData['email'],
          'price' => $packages["num_post"],
          'subject' => "Credit Transaction Approved",
          'message' => "Your account has been credited with ".$packages->num_post
        );

        Mail::send('email.credit_transactions' , ['data' => $data], function($message) use($data)
        {
          $message->to($data['email']);
          $message->cc(env('MAIL_BCC_ADDRESS', 'noreply@cilipadi.com.my'), env('MAIL_BCC_NAME', 'CiliPadi'));
          $message->from(env('MAIL_USERNAME', 'noreply@cilipadi.com.my'), env('MAIL_FROM_NAME', 'CiliPadi'));
          $message->subject($data['subject']);
        });
      }
      return 1;
    }

    public function rejectPendingTransactions(Request $req)
    {
      $array = ["status"=> 2, "reject_reason"=> $req->rejectText, "reject_type" => $req->reasonType];
      PurchaseHistory::where('id', $req->tid)->update($array);
      $userData = PurchaseHistory::where('id', $req->tid)->with('getUser')->first();

      $data = array(
        'email' => $userData['getUser'][0]['email'],
        'price' => "",
        'subject' => "Credit Transaction Rejected",
        'message' => "Your credit request has been rejected. Reason is ".$req->rejectText
      );

      Mail::send('email.credit_transactions' , ['data' => $data], function($message) use($data) {
      $message->to($data['email']);
      $message->cc(env('MAIL_BCC_ADDRESS', 'noreply@cilipadi.com.my'), env('MAIL_BCC_NAME', 'CiliPadi'));
      $message->from(env('MAIL_USERNAME', 'noreply@cilipadi.com.my'), env('MAIL_FROM_NAME', 'CiliPadi'));
      $message->subject($data['subject']);
      });

      return 1;
    }

    public function verifiedCheque()
    {
      $sidebar = $this->sideMenu();
      $chequeDetail = ChequeDetail::with('packageDetail')->with('userData')->where('status',1)->paginate(10);
      return view('getchequelist')->with('cheque_detail',$chequeDetail)->with('sidebar_items', $sidebar);
    }

    public function notverifiedCheque()
    {
      $sidebar = $this->sideMenu();
      $chequeDetail = ChequeDetail::with('packageDetail')->with('userData')->where('status',0)->paginate(10);
      return view('getchequelist')->with('cheque_detail',$chequeDetail)->with('sidebar_items', $sidebar);
    }

    public function verifyCheque(Request $req)
    {
      $array = array("status"=>"1");
      ChequeDetail::where('id',$req->id)->update($array);
      $package = ChequeDetail::with('packageDetail')->where('id' , $req->id)->first();
      $package = $package->packageDetail->toArray();
      $appUser = Auth::user();
      $userId  = $appUser->id;
      AppUser::where('user_id', $userId)->increment('credits', $package[0]['num_post']);
      return 1;
    }

    public function selectedPackages($id)
    {
      $packages = PackageModel::where('id' , $id)->where('enabled', 1)->get();
      return view('admin_package_view')->with('packages', $packages);
    }

    public function viewPackages($id)
    {
      $packages = PackageModel::where('id' , $id)->where('enabled', 1)->get();
      return view('admin_package_view_readonly')->with('packages', $packages);
    }

    public function sideMenu()
    {
      $sidebar = array(
        "Approved Transactions" => array('url' => URL::route('approvedTransactions'), 'icon' => '<i class="fa fa-d"></i>'),
        "Pending Transactions" => array('url' => URL::route('pendingTransactions'), 'icon' => '<i class="fa fa-usrs"></i>'),
        "Edit Payment Details" => array('url' => URL::route('editAccountDetail'), 'icon' => '<i class="fa fa-usrs"></i>'),
      );
      return $sidebar;
    }

    public function userDetail($id)
    {
      //$sidebar = $this->sideMenu();
      $user = AppUser::where('user_id',$id)->get();
      return view('user_detail')->with('user_detail',$user);//->with('sidebar_items', $sidebar);
    }

    public function editAccountDetail()
    {
      $sidebar = $this->sideMenu();
      $payment_info = PaymentInfo::where('id', 1)->first();

      return view('edit_account_detail')->with('sidebar_items', $sidebar)->with('payment_info', $payment_info);
    }

    protected function validateAccount(array $data)
    {
      $arr = ["accountname.required"=> "Account name is required",
        "bankname.required"=> "Bank name is required",
        "bankcode.required"=> "Bank code is required",
        "accountno.required"=> "Account number is required",
        "accountname.max"=> "Account name must be 30 characters long",
        "bankname.regex"=> "Bank name should be characters only",
        "bankname.max"=> "Bank name should be 30 characters long",
        "bankcode.digits_between"=> "Bank code must be between 1 and 10 digits",
        "accountno.digits_between"=> "Account number must be between 1 and 20 digits",
      ];

      $rules = [
        'accountname' => 'required|string|max:30',
        'bankname' => 'required|string|max:30|regex:/^[\pL\s\-]+$/u',
        // 'bankcode' => 'required|digits_between:1,10',
        'accountno' => 'required|digits_between:1,20',
      ];

      return Validator::make($data, $rules, $arr);
    }

    public function submitAccountDetail(Request $req)
    {
      $input = $req->all();
      $validator = $this->validateAccount($input);

      if($validator->passes())
      {
        $updateArray = [];
        $updateArray["account_name"] = $input["accountname"];
        $updateArray["bank_name"] = $input["bankname"];
        // $updateArray["branch_code"] = $input["bankcode"];
        $updateArray["account_no"] = $input["accountno"];

        PaymentInfo::where('id', 1)->update($updateArray);
        $message = trans('header.Account Detail Updated');
        Session::flash('submit', $message);
        return Redirect('admin/editAccountDetail');
      }
      else
      {
        $messages = $validator->messages();
        Session::flash('paymentError', $messages->all());
        return back()->withInput();
      }
    }

    public function addExpirePost(Request $request)
    {
      $expirepost = AdminSetting::find(1);
      return view('expire_post')->with('expirevalue', $expirepost->expirepost);
    }

    public function updateExpirePost(Request $request)
    {
      $expirepost = AdminSetting::find(1);

      $expirepost->expirepost = $request->expirepost;

      $expirepost->save();
      
      return redirect('admin/setExpirePost')->with('status','Updated successfully')->with('expirevalue', $expirepost->expirepost);
    }

    public function addVoucher()
    {
      $voucherBar = $this->voucherMenu();

      $banners = PackageBanner::where('enabled' , '<=' , 1)->get();
      return view('voucher_select_package')->with('packages', $banners)->with('flag', 0)->with('sidebar_items', $voucherBar);
    }

    public function addCreditVoucher()
    {
      $voucherBar = $this->voucherMenu();

      $packages = PackageModel::where('enabled' , '<=' , 1)->get();
      return view('voucher_select_package')->with('packages', $packages)->with('flag', 1)->with('sidebar_items', $voucherBar);
    }

    public function addVoucherDetails($id, $flag)
    {
      $voucherCode = $this->generateRandomString();
      $voucherBar = $this->voucherMenu();

      return view('voucher_add_user')->with('voucherCode', $voucherCode)->with('packageId', $id)->with('flag', $flag)->with('sidebar_items', $voucherBar);
    }

    public function voucherMenu()
    {
      $voucherBar = array(
        "Add Ads Voucher" => array('url' => URL::route('addVoucher'), 'icon' => '<i class="fa fa-usrs"></i>'),
        "Add Credits Voucher" => array('url' => URL::route('addCreditVoucher'), 'icon' => '<i class="fa fa-d"></i>'),
      );
      return $voucherBar;
    }

    protected function validateVoucher(array $data)
    {
      $arr = [];

      $rules = [
        //2018-11-01 by chencw
        //'userEmail' => 'required|string|email|max:255',
        'voucherExpire' => 'required',

      ];

      return Validator::make($data, $rules, $arr);
    }

    public function setAddVoucher(Request $req)
    {
      $input = $req->all();
      $validator = $this->validateVoucher($input);

      if($validator->passes())
      {
        $user = AppUser::where('email', $input['userEmail'])->where('account_activated', 1)->where('email_activated', 1)->get();
        if(count($user) > 0)
        {
          //$expireDate = Carbon::now()->addDay(30);// 30 day expiry of voucher from now
          $expireDate = $input['voucherExpire'];// 2018-11-01 - ChenCW Expire Date

          VoucherModel::create([
            'user_id' => $user[0]['user_id'], 
            'package_id' => $input['pkg'],
            'voucherCode' => $input['tkn'],
            'status' => 1,
            'type' => $input['typ'],
            'expire_date' => $expireDate,
          ]);

          $message = "";

          if($input['typ'] == 0)
          {
            //2018-11-01 by ChenCW
            //$message = "You have been awarded with voucher. You can use this voucher code for purchasing banner. Here is your voucher code ".$input['tkn']." It will expire in 30 days from now.";
            $message = "You have been awarded with voucher. You can use this voucher code for purchasing banner. Here is your voucher code ".$input['tkn']." It will expire on " .$expireDate.".";

          }
          else
          {
            //2018-11-01 by ChenCW
            //$message = "You have been awarded with voucher. You can use this voucher code for purchasing credits. Here is your voucher code ".$input['tkn']." It will expire in 30 days from now.";
            $message = "You have been awarded with voucher. You can use this voucher code for purchasing credits. Here is your voucher code ".$input['tkn']." It will expire on " .$expireDate.".";
          }

          $data = array(
            'email' => $user[0]['email'],
            'subject' => "Voucher",
            'message' => $message
          );

          Mail::send('email.credit_transactions' , ['data' => $data], function($message) use($data)
          {
            $message->to($data['email']);
            $message->cc(env('MAIL_BCC_ADDRESS', 'noreply@cilipadi.com.my'), env('MAIL_BCC_NAME', 'CiliPadi'));
            $message->from(env('MAIL_USERNAME', 'noreply@cilipadi.com.my'), env('MAIL_FROM_NAME', 'CiliPadi'));
            $message->subject($data['subject']);
          });

          return redirect('admin/addVoucher')->with('status','Voucher added successfully');
          //send email to user
        }
        else
        {
          $messages = ['Not a valid email address'];
          Session::flash('EmailError', $messages);
          return Redirect()->back()->withInput();
        }
      }
      else
      {
        $messages = $validator->messages();
        Session::flash('EmailError', $messages->all());
        return Redirect()->back()->withInput();
      }
    }

    public  function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}