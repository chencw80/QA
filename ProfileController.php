<?php

namespace App\Http\Controllers;

use App\AppUser;
use App\UserInfo;
use App\User;
use App\Post;
use App\UserStatementHistory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use File;
use App\State;
use App\NgoCategoriesModel;

class ProfileController extends Controller
{
    public function showProfile()
    {
        $appUser = Auth::user();

        $ngoCategory = '';

        if(!is_null($appUser))
        {
          $profileData = AppUser::where('user_id' , $appUser->id)->first();
        }
        else
        {
          $loggedInId = Session::get('facebookId');
          $profileData = AppUser::where('user_id' , $loggedInId)->first();
        }
        
        if($profileData['account_type'] == 0)
            $ngoCategory = NgoCategoriesModel::where('id', $profileData['ngo_category'])->get();

        $stateData = State::select(['id', 'name'])->get();

        foreach($stateData->toArray() as $statedata)
        {
            $location[] = $statedata;
        }

        $userData = User::where('id', $appUser->id)->first();

        $path = storage_path('app/getCert/' . $profileData['company_certificate_url']);

        $mimeType = '';
	    if (File::exists($path)) {
	        //abort(404);
	        $mimeType = File::mimeType($path);
	    }

        return view('auth.profile')->with(compact('profileData'))->with('userImage', $userData['image'])->with('ngoCategory',$ngoCategory)->with('location', $location)->with(compact('mimeType'));
    }

    public function showDashboard()
    {
        $appUser = Auth::user();

        $userData = AppUser::where('user_id', $appUser->id)->get();

        $livePosts = Post::where('user_id', $appUser->id)->where('post_status', 1)->count();

        return view('show_dashboard')->with(compact('userData'))->with('livePosts', $livePosts);
    }

    protected function validatorAccount(array $data, $type)
    {   
        //20181030 changed by SHC 20180820_CP_003 - Start
        if(isset($data['phone_number']))
        {
            if($data['phone_number'][0] == 0)
                $data['phone_number'][0] = 1;
            $data['phone_number'] = (int)$data['phone_number'];
        }

        //Log::info("validatorAccount phone number: ".$data['phone_number'] );
        //20181030 changed by SHC 20180820_CP_003 - End

        $arr = [
            "imgUpload.mimes" => "Only allowed file type is jpg, png, bmp, jpeg",
            //20181030 changed by SHC 20180820_CP_003 - Start
            "phone_number.unique" => "The mobile phone number has already been taken",
            "phone_number.required" => "The Mobile Phone Number field is required",
            "phone_number.digits_between"=>"The mobile phone number must be between 10 to 11 digits",
            //20181030 changed by SHC 20180820_CP_003 - End
        ];

        $rules = [];

        if($type == 0)
        {
            $arr["ngoCategory.required"] = "NGO Category is required";
            $rules = [
                      'ngoCategory' => 'required',
                    ];
        }

        //20181030 changed by SHC 20180820_CP_003 
        $rules['phone_number']= 'required|integer|digits_between:10,11';

        if($type != 1)
        {
            $arr["imgUpload.max"] = "Company logo not be greater than 10MB";
        }
        else
        {
            $arr["imgUpload.max"] = "Profile photo not be greater than 10MB";
        }

        if(array_key_exists('imgUpload',$data))
          $rules['imgUpload'] = 'max:10240|mimes:jpeg,bmp,png,jpg,gif';

        return Validator::make($data, $rules, $arr);
    }

    protected function validator(array $data)
    {
        if(isset($data['companyPhone']))
        {
            if($data['companyPhone'][0] == 0)
                $data['companyPhone'][0] = 1;
            $data['companyPhone'] = (int)$data['companyPhone'];
        }
        if(isset($data['companyFax']))
        {
            if($data['companyFax'][0] == 0)
                $data['companyFax'][0] = 1;
            $data['companyFax'] = (int)$data['companyFax'];
        }
        if(isset($data['postcode']))
        {
            if($data['postcode'][0] == 0)
                $data['postcode'][0] = 1;
            $data['postcode'] = (int)$data['postcode'];
        }
        
        $arr = [
            "companyName.required"=> "The company name field is required",
            "companyRegNumber.required"=> "The company registeration number is required",
            "companyAddress.required"=> "The company address is required",
            "companyPhone.required"=> "The company phone number field is required",
            "companyFax.required"=> "The company fax is required",
            "rosCertificate.required"=> "ROS/ROC certificate required",
            "rosCertificate.max"=> "ROS/ROC certificate not be greater than 10MB",
            "rosCertificate.mimes" => "Only allowed file type is rtf, pdf, png, bmp, jpeg",
            //20181025 Change by SHC 20180816_CP_002 - Start
            "rosCertificate2.max"=> "ROS/ROC certificate 2 not be greater than 10MB",
            "rosCertificate2.mimes" => "Only allowed file type is rtf, pdf, png, bmp, jpeg",
            "rosCertificate3.max"=> "ROS/ROC certificate 3 not be greater than 10MB",
            "rosCertificate3.mimes" => "Only allowed file type is rtf, pdf, png, bmp, jpeg",
            "phone_number.unique_space_check" => "The mobile phone number has already been taken",
            //20181025 Change by SHC 20180816_CP_002 - End
            "companyPhone.digits_between"=>"The company phone number must be between 9 to 10 digits",
            "postcode.digits_between"=>"Post code must be between 4 to 5 digits",
            "companyFax.digits_between"=>"The company fax number must be between 9 to 10 digits",
            "state.required"=>"State is required",
        ];

        $rules = [
          'companyName' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
          'companyRegNumber' => 'required',
          'companyAddress' => 'required|string',
          'state' => 'required',
          'companyPhone' => 'required|integer|digits_between:9,10',
          //'companyFax' => 'required|integer|digits_between:9,10', //20181025 Change by SHC 20180816_CP_001 
          'postcode' => 'required|integer|digits_between:4,5',
          'rosCertificate' => 'max:10240|mimes:jpeg,bmp,png,application/vnd.openxmlformats-officedocument.wordprocessingml.document,rtf,pdf',
        ];
        //20181025 Change by SHC 20180816_CP_002 - Start          
            if (Input::hasFile('rosCertificate2')) {
                    $rules['rosCertificate2'] = 'max:10240|mimes:jpeg,bmp,png,application/vnd.openxmlformats-officedocument.wordprocessingml.document,rtf,pdf';
            }
            if (Input::hasFile('rosCertificate3')) {
                $rules['rosCertificate3'] = 'max:10240|mimes:jpeg,bmp,png,application/vnd.openxmlformats-officedocument.wordprocessingml.document,rtf,pdf';
            }
        
        //20181025 Change by SHC 20180816_CP_002 - End
            //20181025 Change by SHC 20180816_CP_001 
            //$rules['companyFax'] = 'required|integer|digits_between:9,10';
            if($data['companyFax']!=''){
                $rules['companyFax'] = 'integer|digits_between:9,10';
            }
        return Validator::make($data, $rules, $arr);
    }

    public function admin_credential_rules(array $data)
    {
        $messages = [
          'current-password.required' => 'Please enter current password',
          'current-password.min' => 'Old Password must be at least 8 characters',
          'password.min' => 'New Password must be at least 8 characters',
          'password_confirmation.same' => 'New Password and Re-enter password must be same',
          'password.required' => 'Please enter new password',
          'password_confirmation.required' => 'Please enter confirm password',
          
        ];

        $validator = Validator::make($data, [
          'current-password' => 'required|string|min:8',
          'password' => 'required|string|min:8|same:password',
          'password_confirmation' => 'required|string|min:8|same:password',

        ], $messages);

        return $validator;
    }

    public function postCredentials(Request $request)
    {
        if(Auth::Check())
        {
            $request_data = $request->All();
            $validator = $this->admin_credential_rules($request_data);
            if($validator->fails())
            {
                $messages = $validator->messages();
                Session::flash('changePassError', $messages->all());
                return redirect()->back();
            }
            else
            {
                $current_password = Auth::User()->password;
                if(Hash::check($request_data['current-password'], $current_password))
                {
                    $user_id = Auth::User()->id;
                    $obj_user = User::find($user_id);
                    $obj_user->password = Hash::make($request_data['password']);
                    $obj_user->save();
                    return redirect()->to('passChangeSuccess');
                }
                else
                {   
                    Session::flash('changePassError', [trans('header.Enter Correct Password')]);
                    return redirect()->back();
                }
            }
        }
        else
        {
            return redirect()->to('/');
        }
    }

    public function passChangeSuccess()
    {
        return view('passwordChangeSuccess');
    }

    public function accountChangeSuccess()
    {
        return view('accountChangeSuccess');
    }

    public function changepassword()
    {
        return view('/changepassword');
    }

   public function editAccountInfo()
    {
        $appUser = Auth::user();

        $ngoCategories = [];

        $profileData = AppUser::where('user_id' , $appUser->id)->first();

        if($profileData['account_type'] == 0)
        {
            $ngoCategoriesData = NgoCategoriesModel::where('status', '1')->select(['id', 'category_name'])->get();

            foreach($ngoCategoriesData->toArray() as $cat)
            {
                $ngoCategories[$cat['id']] = $cat['category_name'];
            }
        }

        $userData = User::where('id', $appUser->id)->first();

        return view('edit_profile_account')->with(compact('profileData'))->with('userImage', $userData['image'])->with('ngoCategories',$ngoCategories);
    }

    public function updateProfileAccount(Request $request)
    {
        $input = $request->all();
               //20181030 changed by SHC 20180820_CP_003 - Start
        if(isset($input['phone_number']))
        {
            $exp = ['(',')','-',' ','_'];
            $input['phone_number'] = str_replace($exp, '', $input['phone_number']);
        }
        //20181030 changed by SHC 20180820_CP_003 - End
        $appUser = Auth::user();

        $user = AppUser::where('user_id' , $appUser->id)->first();

        $validator = $this->validatorAccount($input, $user->account_type);

        if($validator->passes())
        {
            if($user->account_type == 0 && isset($input['ngoCategory']))
            {
                $user->ngo_category = $input['ngoCategory'];
            }

            $user->phone_number = $input['phone_number']; //20181030 changed by SHC 20180820_CP_003 
            

            $laravelUser = User::where('id' , $appUser->id)->first();

            if(Auth::user()->provider!='facebook' && $request->file('imgUpload'))
            {
                if(Auth::user()->image!=null)
                {
                    File::delete(public_path() . '/profile_image/'.Auth::user()->image);
                }
                $fileName = $this->addDocument($request);
                $laravelUser['image'] = $fileName;
            }

            $user->save();
            $laravelUser->save();

            return redirect()->to('accountChangeSuccess');
        }
        else
        {
            $messages = $validator->messages();
            Session::flash('profileError', $messages->all());
            return redirect('/editAccountInfo');
        }
    }

    public function editCompanyInfo()
    {
        $appUser = Auth::user();

        $profileData = AppUser::where('user_id' , $appUser->id)->first();
        
        $stateData = State::select(['id', 'name'])->get();

        foreach($stateData->toArray() as $statedata)
        {
            $location[$statedata['id']] = $statedata['name'];
        }

        $path = storage_path('app/getCert/' . $profileData['company_certificate_url']);

        $mimeType = '';
	    if (File::exists($path)) {
	        //abort(404);
	        $mimeType = File::mimeType($path);
	    }

        return view('edit_profile_company')->with(compact('profileData'))->with('location', $location)->with(compact('mimeType'));
    }

    public function updateProfile(Request $request)
    {
        $input = $request->all();

        $appUser = Auth::user();
        $user = AppUser::where('user_id' , $appUser->id)->first();

        if(isset($input['companyPhone']))
        {
            $exp = ['(',')','-',' ','_'];
            $input['companyPhone'] = str_replace($exp, '', $input['companyPhone']);
        }

        if(isset($input['companyFax']))
        {
            $exp = ['(',')','-',' ','_'];
            $input['companyFax'] = str_replace($exp, '', $input['companyFax']);
        }

        $validator = $this->validator($input);

        if($validator->passes())
        {
            if(isset($input['companyName']))
                $user->company_name = $input['companyName'];

            if(isset($input['companyRegNumber']))
                $user->company_register_num = $input['companyRegNumber'];

            if(isset($input['companyAddress']))
                $user->address_1 = $input['companyAddress'];

            //if(isset($input['companyAddress2'])) //20181025 Change by SHC 20180816_CP_001 
                $user->address_2 = $input['companyAddress2'];

            if(isset($input['postcode']))
                $user->address_postcode = $input['postcode'];

            if(isset($input['city']))
                $user->address_city = $input['city'];

            if(isset($input['state']))
                $user->address_state = $input['state'];

            if(isset($input['country']))
                $user->address_country = $input['country'];

            if(isset($input['companyPhone']))
                $user->company_phone_num = $input['companyPhone'];

            //if(isset($input['companyFax'])) //20181025 Change by SHC 20180816_CP_001 
                $user->company_fax_num = $input['companyFax'];

            if($request->file('rosCertificate'))
            {
                $certFileName = $this->addCertificate($request,0);

                if($user->company_certificate_url!=null)
                {
                    File::delete(public_path() . '/getCert/'.$user->company_certificate_url);
                }

                $user->company_certificate_url = $certFileName;
            }
            
            if($request->file('rosCertificate2'))
            {
                $certFileName2 = $this->addCertificate($request,1);

                if($user->company_certificate_url2!=null)
                {
                    File::delete(public_path() . '/getCert/'.$user->company_certificate_url2);
                }

                $user->company_certificate_url2 = $certFileName2;
            }
            if($request->file('rosCertificate3'))
            {
                $certFileName3 = $this->addCertificate($request,2);

                if($user->company_certificate_url3!=null)
                {
                    File::delete(public_path() . '/getCert/'.$user->company_certificate_url3);
                }

                $user->company_certificate_url3 = $certFileName3;
            }
            $user->save();

            return redirect('/showprofile');
        }
        else
        {
            $messages = $validator->messages();
            Session::flash('profileError', $messages->all());
            return redirect('/editCompanyInfo');
        }
    }

    public function myInfo()
    {
        $profiletext = UserInfo::where('user_id',Auth::User()->id)->select('body','id')->first();
        return view('my_info',compact('profiletext'));
    }

    public function infoDetails($id)
    {
        $userinfo = UserInfo::where('user_id', $id)->select('body','id','user_id')->first();
        if(is_null($userinfo))
            return redirect()->back();
        $username = AppUser::where('user_id',$userinfo->user_id)->first();
        return view('my_info_detail',compact('userinfo','username'));
    }
      
    public function submitMyInfo(Request $request)
    {
        $userid = Auth::user();
        if ($request->input('profileId'))
        {
            $userinfo = UserInfo::find($request->input('profileId'));
            $userinfo->body = $request->body;
            $userinfo->status = '1';
        }
        else
        {
            $userinfo = new UserInfo;
            $userinfo->user_id = $userid->id;
            $userinfo->body = $request->body;
            $userinfo->status = '1';
        } 
        $userinfo->save();
        return redirect('/');
    }
      
    public function addDocument(Request $request)
    {
        $ext = Input::file('imgUpload')->getClientOriginalExtension();
        $fileName = time().".".$ext;
        $uploadedFolder = public_path() . '/profile_image';
        Input::file('imgUpload')->move($uploadedFolder, $fileName);
        if (Input::hasFile('imgUpload'))
            return $fileName;
    }

    public function addCertificate(Request $request,$ind)
    {
        //log::info("Add Document ind: ".$ind);
        $fields = ['rosCertificate', 'rosCertificate2', 'rosCertificate3']; 
        //log::info("Add Document ".$ind." Client file name [".$_FILES[$fields[$ind]]['name']."] ErrNo:".$_FILES[$fields[$ind]]['error'] );
        $ext = Input::file($fields[$ind])->getClientOriginalExtension();
        $fileName =  time().".".$ext;   
        //log::info("Add Document upload fileName: ".$fileName );

        $uploadedFolder = public_path() . '/getCert';
        //log::info("Add Document UploadFolder: ".$uploadedFolder );
        Input::file($fields[$ind])->move($uploadedFolder, $fileName);
        //log::info("Add Document uploaded: ".$uploadedFolder);
        if (Input::hasFile($fields[$ind])) {
            return $fileName;
        }
    }

    public function downloadStatement()
    {
        $userid = Auth::user()->id;
        $statement = UserStatementHistory::where('user_id', $userid)->where('status', 1)->select(['report'])->first();
        $fileName = $statement['report'];
        $path = storage_path('app/public/userStatements/'.$fileName);
        return response()->download($path);
    }
}